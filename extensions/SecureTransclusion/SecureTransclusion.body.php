<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureTransclusion
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class SecureTransclusion
{
	const thisType = 'other';
	const thisName = 'SecureTransclusion';
	
	public function mg_strans( &$parser, $page, $errorMessage = null, $timeout = 5 )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'SecureTransclusion: '.wfMsg('badaccess');
		
		$title = Title::newFromText( $page );
		if (!is_object( $title ))
			return 'SecureTransclusion: '.wfMsg('badtitle');
		
		if ( $title->isTrans() )
			return $this->getRemotePage( $parser, $title, $errorMessage, $timeout );
		
		return $this->getLocalPage( $title, $errorMessage );
	}
	/**
	 * Retrieves a local page.
	 */
	protected function getLocalPage( &$title, $error_msg )
	{
		$contents = $error_msg;
		$rev = Revision::newFromTitle( $title );
		if( is_object( $rev ) )
		    $contents = $rev->getText();		
		return $contents;		
	}
	/**
	 * Retrieves a page located on a remote server.
	 */
	protected function getRemotePage( &$parser, &$title, &$error_msg, $timeout )
	{
		$uri = $title->getFullUrl();

		// just encode the string to make sure
		// we don't break anything downstream.
		$euri = urlencode( $uri );		
		$text = $this->fetch( $euri, $timeout );
		
		// if we didn't succeed, turn off parser caching
		// hoping to get lucky next time around.
		if (false === $text)
		{
			$parser->disableCache();
			$text = $error_msg;
		}
			
		return $text;
	}	 
	/**
	 *
	 */
	private static function checkExecuteRight( &$title )
	{
		/*
		global $wgUser;
		if ($wgUser->isAllowed('strans'))
			return true;
		*/
		if ($title->isProtected('edit'))
			return true;
		
		// Last resort; check the last contributor.
		/*
		$rev    = Revision::newFromTitle( $title );
		
		$user = User::newFromId( $rev->mUser );
		$user->load();
		
		if ($user->isAllowed( 'strans' ))
			return true;
		*/
		return false;
	}
	/**
	 * Gets from the cache
	 */
	protected function getFromCache( &$uri )
	{
		$parserCache =& ParserCache::singleton();
		return $parserCache->mMemc->get( $uri );
	}
	/**
	 * Saves in the cache
	 */
	protected function saveInCache( $uri, &$text, $timeout )
	{
		$parserCache =& ParserCache::singleton();
		
		// keep in cache a little longer to give time to the HEAD lookup 
		// to determine if the source page has really changed
		return $parserCache->mMemc->set( $uri, $text, $timeout );
	}
	/**
	 *  Fetches an external page from either the parser cache or external uri
	 *  If we get here, it means either:
	 *  1) The parser cache entry for the page has expired
	 *  2) Parser caching is not in use thus the page must be fetched on every page view (expensive !)
	 *  
	 *  Cases to cover:
	 *  case 1: Etag of remote page === Etag of locally cached page
	 *  		=> just use the locally cached page
	 *  		=> refresh cached Etag
	 *  
	 *  case 2: Etag of remote page !== Etag of locally cached page
	 *  		=> fetch remote page
	 *  		=> store in cache (page & Etag)
	 *  
	 *  case 3: Etag of remote page NOT available
	 *  		=> return locally cached page (if available)
	 *  		=> if not available, try fetching it
	 */	
	protected function fetch( $uri, $timeout )
	{
		$rEtag = null;
		$lEtag = null;
		 
		$r = $this->compareEtags( $uri, $rEtag, $lEtag );
		switch ( $r )
		{
			case true:
				return $this->doCase1( $uri, $rEtag, $timeout );
			case false:
				return $this->doCase2( $uri, $rEtag, $timeout );
			case null:
				break;
		}		
			return $this->doCase3( $uri, $rEtag, $lEtag, $timeout );
	}
	/**
	 * Etag remote === Etag local
	 */
	protected function doCase1( &$uri, &$etag, $timeout ) 
	{
		$text = $this->getFromCache( $uri );
		
		// if we run into a problem, try case #2.
		if ( $text === false ) 
			return $this->doCase2( $uri, $etag, $timeout );

		// refresh Etag in cache
		$this->saveEtagInCache( $uri, $etag );
		
		return $this->saveInCache( $uri, $text, $timeout );
	}
	/**
	 * Etag remote !== Etag local
	 */
	protected function doCase2( &$uri, &$etag, $timeout ) 
	{
		$text = $this->getRemotePage( $uri, $timeout );
		
		// if we can't fetch from the remote server, bail out.
		if ( $text === false )
			return false;

		// save Etag in cache
		$this->saveEtagInCache( $uri, $etag );
		
		return $this->saveInCache( $uri, $text, $timeout );		
	}
	/**
	 * 
	 */
	protected function doCase3( &$uri, &$rEtag, &$lEtag, $timeout ) 
	{
		$text = $this->getFromCache( $uri );
		
		// if we run into a problem, try case #2.
		if ( $text === false ) 
			return $this->doCase2( $uri, $etag, $timeout );
		
	}
	/**
	 * 
	 * @return 
	 * @param $uri string
	 */	
	protected function fetchRemotePage( &$uri )
	{
		// try to fetch from cache
		$text = $this->getFromCache( $euri );
		if ( $text === false)
		{
			$text = Http::get( $uri, $timeout );
			if ( $text !== false )
				$this->saveInCache( $euri, $text, $timeout + 86400 /*1day*/  );
		}
		
		return $text;
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// ETAG related
		
	/**
	 * Compare Etags
	 * 
	 * @return bool
	 */
	protected function compareEtags( &$uri, &$rEtag, &$lEtag )
	{
		$rEtag = $this->getRemoteEtag( $uri );
		$lEtag = $this->getEtagFromCache( $uri );
		
		if ( $rEtag === null ) return null;
		return ( $rEtag === $lEtag );
	}
	/**
	 * Get E-tag
	 * Requires pecl module ''pecl_http''
	 *
	 */
	protected function getRemoteEtag( &$uri )
	{
		if (!$this->checkEtagProcessing())
			return null;

		$head = @http_head( $uri );
		if ( $head === false )
			return false;
			
		$r = preg_match( "/Etag:.\"(.*)\"\n/i", $head, $match );
		if ( ( $r === false ) || ($r === 0) )
			return false;
			
		return $match[1];
	}
	/**
	 * Save Etag in cache
	 */
	protected function saveEtagInCache( &$uri, &$etag )
	{
		return $this->saveInCache( $uri.'-etag', $etag, 86400 /*1day*/ );
	}
	/**
	 * Get Etag from the cache
	 */
	protected function getEtagFromCache( &$uri )
	{
		return $this->getFromCache( $uri.'-etag' );
	}
	/**
	 *  Check for Etag processing capability
	 */ 
	protected function checkEtagProcessing()
	{
		 return function_exists('http_head');
	}
} // end class
//</source>
