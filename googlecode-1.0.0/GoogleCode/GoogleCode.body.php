<?php
/**
 * @author Jean-Lou Dupont
 * @package GoogleCode
 * @version $Id$
*/
//<source lang=php>

require('GoogleCode.i18n.php');

class GoogleCode
{
	const thisType = 'other';
	const thisName = 'GoogleCode';
	
	static $msg = array();
	
	public function __construct() 
	{
		global $wgMessageCache;
		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}
	/**
		{{#gcode: project=PROJECT NAME|file=FILENAME }}
	 */
	public function mg_gcode( &$parser )
	{
		$args = func_get_args();
		$argv = StubManager::processArgList( $args, true );

		$project = @$argv['project'];
		$file    = @$argv['file'];
		$result = $this->validateParameters( $project, $file );				

		// don't bother going forward if we already have an error message
		if (!empty( $result ))
			return $result;

		$code = $this->getCode( $project, $file, $result );	

		// don't bother going forward if we already have an error message
		if (!empty( $result ))
			return $result;

		return $code;		
	}
	
	/**
		<gcode project='' file='' lang='' />
	 */
	public function tag_gcode( &$texte, &$argv, &$parser )
	{
		$project = @$argv['project'];	
		$file    = @$argv['file'];
		$lang    = @$argv['lang'];
		
		$result = $this->validateParameters( $project, $file );
		
		if (empty( $lang ))
			$result .= wfMsg('googlecode').wfMsg('googlecode'.'-missing-lang')."<br/>";
			
		// don't bother going forward if we already have an error message
		if (!empty( $result ))
			return $result;
		
		$code = $this->getCode( $project, $file, $result );	

		// don't bother going forward if we already have an error message
		if (!empty( $result ))
			return $result;
		
		// use an installed syntax highlighter
		return $this->highlight( $code, $lang );
	}
	/**
		Gets the code from the Google Code repository 
		if not already in the cache.
	 */
	protected function getCode( &$project, &$file, &$result )
	{
		$result = null;
		
		// check the cache first.
		$code = cacheCode::exists( $project, $file ); 
		if ($code !== null)
			return $code;
		
		$error = GoogleCodeSite::getCode( $project, $file, $code );
		if ($error !== CURLE_OK)
		{
			$result = wfMsg('googlecode').wfMsg('googlecode'.'-error-accessing-URI', $project, $file )."<br/>";
			return $null;
		}
		
		// cache the file just in case we need it later.
		cacheCode::add( $project, $file, $code );

		return $code;		
	}
	protected function validateParameters( &$project, &$file )
	{
		$result = '';
		
		if (empty( $project ))
			$result .= wfMsg('googlecode').wfMsg('googlecode'.'-missing-project')."<br/>";

		if (empty( $file ))
			$result .= wfMsg('googlecode').wfMsg('googlecode'.'-missing-file')."<br/>";

		return $result;		
	}
	/**
		To highlight the source code, we rely on an installed geshi extension
		that processes the 'source' tag.
	 */
	public function highlight( &$document, &$lang )
	{
		global $wgParser;
		$parser = clone $wgParser;
	
		$doc = "<"."source lang='".$lang."'>".$document.'</'.'source>';
		
		$po = $parser->parse( $doc, new Title() /* title object */, new ParserOptions() );
		
		return $po->getText();
	}
	
} // end class


class GoogleCodeSite
{
	// base URI of googlecode
	const baseURI  = 'http://$1.googlecode.com/svn/';
	
	const port 		= 80;
	const timeout	= 15;

	public static function getCode( &$project, &$file, &$code )
	{
		$uri = self::formatURI( $project, $file );	
		
		return self::getPage( $uri, $code );
	}

	/**
		Formats the URI securily.
	 */
	public static function formatURI( &$project, &$file )
	{
		$project = htmlspecialchars( $project );
		$file    = htmlspecialchars( $file );
		
		$uri = self::baseURI.$file;
		$uri = str_replace( '$1', $project, $uri );
		return $uri;
	}
	/**
		Uses the CURL library to fetch the code off Google's WEB site.
	 */
	public static function getPage( &$uri, &$document )
	{
		 // initialize curl handle
		$ch = curl_init();

		// set url to post to
		curl_setopt($ch, CURLOPT_URL, $uri);
		
		// Fail on errors		
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		// return into a variable		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 	
		//Set the port number				
		curl_setopt($ch, CURLOPT_PORT, self::port );
		// times out after 15s		
		curl_setopt($ch, CURLOPT_TIMEOUT, self::timeout );
		
		$document = curl_exec($ch);
		
		$error = curl_errno($ch);
		curl_close($ch);
		
		// CURLE_OK if everything OK.
		return $error;
	}
		
} // end class


class cacheCode
{
	static $code = array();
	
	public static function add( &$project, &$file, &$code, &$lang = null )
	{
		self::$code[] = array(	'project'	=> $project,
								'file'		=> $file,
								'lang'		=> $lang,
								'code'		=> $code
							);
	}	
	public function exists( &$project, &$file )
	{
		foreach( self::$code as &$e )
			if ( ( $e['project'] == $project ) && ( $e['file'] == $file) )
				return $e['code'];
				
		return null;
	}
} // end class

//</source>
