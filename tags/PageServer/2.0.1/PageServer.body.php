<?php
/**
 * @author Jean-Lou Dupont
 * @package PageServer
 * @category ExtensionServices
 * @version 2.0.1
 * @Id $Id: PageServer.body.php 991 2008-04-07 14:12:08Z jeanlou.dupont $
 */
//<source lang=php>
class PageServer
{
	const thisType = 'other';
	const thisName = 'PageServer';
	
	/**
	 * Base directory in PEAR for
	 * the extensions
	 * 
	 * @private
	 */
	static $pear = 'MediaWiki';
	 	
	/**
	 * Local Parser instance
	 * @private
	 */
	static $parser;
	
	public function __construct() 
	{ }
	
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// SERVICES to other extensions
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	/**
		Called using PageServer::loadPage()
	 */
	public static function loadPage( &$filename )
	{
		return @file_get_contents( $filename );	
	}

	/**
		Called using PageServer::loadAndParse()

		This function is targeted at 'template' pages where newline characters
		are used to document the said page but would otherwise cause 
		unnecessary 'holes' once processed.
		
		E.g. use when combining an 'header' page to a 'form' page where the
		'header' page consists mostly of commands/templates etc.

	 */
	public static function loadAndParse( &$filename, &$title, $minify = false )
	{
		$contents = @file_get_contents( $filename );
		if (empty( $contents ))
			return null;
		
		if ($minify)
			$contents = str_replace("\n", '', $contents );
			
		self::initParser();
		$po = self::$parser->parse( $contents, $title, new ParserOptions() );
		
		return $po->getText();
	}
	public static function load( &$filename, $minify = false )
	{
		$contents = @file_get_contents( $filename );
		if (empty( $contents ))
			return null;
		
		if ($minify)
			$contents = str_replace("\n", '', $contents );

		return $contents;		
	}

	/**
	 * Page loading service to other extensions
	 * NOTE that $prefix is only useful for page located in the
	 *      MediaWiki database
	 * 
	 * @param $prefix prefix of the page in case it is found in the database
	 * @param $name   name of page
	 * @param $result holds the result
	 */
	public function hpage_server( &$prefix, &$name, &$result )
	{
		// The calling party should place $result = NULL
		// and verify that $result !== NULL => extension present.

		$result = $this->fetch_page( $prefix, $name );
		
		return true;	
	}	
	/**
	 * Loads a page from one of these sources (in priority order):
	 * 1) Parser Cache
	 * 2) PEAR directory
	 * 3) MediaWiki installation directory
	 * 
	 * @return $result string
	 * @param $prefix string
	 * @param $name string
	 */
	protected function fetch_page( &$prefix, &$name )
	{
		$result = null;
		
		// first, let's try the parser cache
		$article = $this->buildArticle( $prefix, $name );
		if ( is_object( $article ))
			if ( $article->getID() !=0 )
				$result = $this->fetchParserCache( $article );	
				
		// next, let's try the database directly
		// happens when parser caching isn't available (unfortunately)				
		if ( !is_string( $result ))
			$result = $this->fetchDatabase( $prefix, $name );
				
		// next, try PEAR directory
		if ( !is_string( $result ))
			$result = $this->fetchPear( $name );
		
		global $IP;
		// finally, try the /extensions directory
		if ( empty( $result ))
			$result = @file_get_contents( $IP.'/extensions/'.$name );
		
		return $result;
	}
	/**
	 * Fetchs a 'page' from the PEAR directory
	 * 
	 * @return $result string
	 * @param $name string
	 */
	protected function fetchPear( &$name )
	{
		$pearPath = $this->findPearPath();
		return @file_get_contents( $pearPath.'/'.self::$pear.'/'.$name );
	}
	/**
	 * Builds a valid article object
	 * 
	 * @return $article Object
	 * @param $prefix string
	 * @param $name string
	 */
	protected function buildArticle( &$prefix, &$name )
	{
		// build a title object
		$title = Title::newFromText( $prefix.$name );
		if (!is_object( $title ))
			return false;
			
		return new Article( $title );
	}
	/**
	 * Fetches a given article from the parser cache
	 * 
	 * @return $result string
	 * @param $article Object
	 */
	protected function fetchParserCache( &$article )
	{
		global $wgUser;
				
		$parserCache =& ParserCache::singleton();
		$po = $parserCache->get( $article, $wgUser );
		if ( is_object( $po ))
			return $po->getText();
			
		return null;
	}
	/**
	 * Fetches a page directly from the database
	 * 
	 * @return $result string
	 * @param $prefix string
	 * @param $name string
	 */
	protected function fetchDatabase( &$prefix, &$name )
	{
		$contents = null;
		$title = Title::newFromText( $prefix.'/'.$name );
		$rev = Revision::newFromTitle( $title );
		if( $rev )
		    $contents = $rev->getText();		

		return $contents;
	}
	
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// PARSER FUNCTIONS
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * Parser Function: #load_page
	 */
	public function mg_load_page( &$parser, $prefix, $name )	
	{
		$title = $parser->mTitle;
		if ( !$title->isProtected( 'edit' ) )
			return 'PageServer: '.wfMsg('badaccess');
			
		return $this->fetch_page( $prefix, $name );
	}
	/**
	 * Parser Function: #mwmsg
	 */
	public function mg_mwmsg( &$parser, $msgId )
	{
		return wfMsg( $msgId );	
	}

	/**
	 * Parser Function: #mwmsgx
	 */
	public function mg_mwmsgx( &$parser, $msgId, $p1 = null, $p2 = null, $p3 = null, $p4 = null )
	{
		return wfMsgForContent( $msgId, $p1, $p2, $p3, $p4 );	
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// HELPER FUNCTIONS
	// NOTE: CAN'T BE CALLED BY OTHER EXTENSIONS
	//	
	private static function initParser()
	{
		if (self::$parser !== null)	
			return;

		// get a copy of wgParser handy.
		global $wgParser;
		self::$parser = clone $wgParser;
	}
	/**
	 * Finds the path of the PEAR repository
	 * 
	 * @return $path string
	 */
	protected function findPearPath()
	{
		$pathArray = explode( PATH_SEPARATOR, get_include_path() );
		
		if ( empty( $pathArray ))
			return null;
			
		foreach( $pathArray as &$e )
			if ( preg_match( '/pear/si', $e ) === 1 )
				return $e;
									
		return null;			
	} 
	
} // end class

//</source>
