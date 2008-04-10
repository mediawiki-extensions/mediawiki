<?php
/**
 * @author Jean-Lou Dupont
 * @package PageServer
 * @category ExtensionServices
 * @version @@package-version@@
 * @Id $Id$
 * @dependency PEAR HTTP::Request
 */
//<source lang=php>
class PageServer_Remote
{
	/**
	 * @private
	 */
	static $_etagMarker = '<!--ETAG:$1-->';

	/**
	 * Fetches a remote page and embeds the 'etag'
	 * in a special HTML comment
	 * 
	 * @return $contents string
	 */
	public static function getAndProcessRemotePage( &$uri, &$etag )
	{
		$contents = self::getRemotePage( $uri, $etag );
		
		if ( !is_string( $contents ))
			return false;
			
		$marker = str_replace( '$1', $etag, self::$_etagMarker );
		$contents = $marker.$contents;
		
		return $contents;
	}
	/**
	 * Fetches a remote page
	 * 
	 * @return $result boolean 
	 * @param $uri string
	 * @param $embedTag boolean[optional]
	 */	
	public static function getRemotePage( &$uri, &$etag = null )
	{
		$req_headers = array();
		
		// make sure we are not fetching a page
		// we already have ... i.e. check Etag
		if (!is_null( $etag ))
			$req_headers['If-None-Match'] = $etag;
		
		// do away with some PHP engine notices
		$response_headers = null;
		$body = null;
		$code = null;
		
		// TODO: better timeout handling?
		$result = self::wget( $uri, 5, $req_headers, $response_headers, $body, $code );
		
		if ( $code == '200' && $result )
		{
			// return the new etag
			$etag = @$response_headers['etag'];
			return $body;
		}

		return false;
	}
	/**
	 * Fetches a page through HTTP.
	 * 
	 * @return $result boolean
	 * @param $uri string URI
	 * @param $timeout
	 * @param $req_headers
	 * @param $response_headers
	 * @param $body string page contents
	 */	
	public static function wget( &$uri, $timeout, &$req_headers, &$response_headers, &$body, &$code )
	{
		$request =& new HTTP_Request( $uri );
										
		$request->_timeout = $timeout;
		
		// REDIRECTS are a requirement
		$request->_allowRedirects = true;
		
		$request->setMethod( 'GET' );

		// do request headers
		foreach( $req_headers as $field => &$entry )
				$request->addHeader( $field, $entry );

		try
		{
			$code = $request->sendRequest();			
		}
		catch(Exception $e)
		{
			$code = null;
		}
		
		if ( PEAR::isError( $code ) || is_null( $code ) )
			return false;

	    $response_headers =	$request->getResponseHeader();
		$body             = $request->getResponseBody();
		$code             = $request->getResponseCode();
		
		return true;
	}
	/**
	 * Extracts the Etag from the page contents.
	 * 
	 * @return $etag string
	 * @param $contents string
	 */	
	public static function extractEtag( &$contents )
	{
		if (empty( $contents ))
			return null;
			
		$result = preg_match( '/\<\!--ETAG\:(.*)--\>/siU', $contents, $match );	
		if ( $result !== 1 )
			return null;
			
		return $match[1];
	}
	
} // end class
//</source>