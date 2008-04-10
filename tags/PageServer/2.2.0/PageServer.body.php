<?php
/**
 * @author Jean-Lou Dupont
 * @package PageServer
 * @category ExtensionServices
 * @version 2.2.0
 * @Id $Id: PageServer.body.php 1009 2008-04-10 00:28:46Z jeanlou.dupont $
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
	 	
	static $_expiry         = 86400;	//24*60*60 == 1day
	const CACHE_PREFIX      = "pageserver-p";
	const CACHE_PREFIX_FLAG = "pageserver-f";	
		
	const SOURCE_ERROR  = 0;
	const SOURCE_CACHE  = 1;
	const SOURCE_REMOTE = 2;

	const STATE_ERROR       = 0;	
	const STATE_CACHE_OK    = 1;
	const STATE_CACHE_STALE = 2;
		
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
	 * Used to load a page from the filesystem.
	 * Called using PageServer::loadPage().
	 * 
	 * @return $contents string
	 * @param $filename string
	 */
	public static function loadPage( &$filename )
	{
		return @file_get_contents( $filename );	
	}
	/**
	 * Used to complete the ''load'' method with parsing
	 * Called using PageServer::loadAndParse()
	 * This function is targeted at 'template' pages where newline characters
	 * are used to document the said page but would otherwise cause 
	 * unnecessary 'holes' once processed.
	 * 
	 * E.g. use when combining an 'header' page to a 'form' page where the
	 * 'header' page consists mostly of commands/templates etc.
	 * 
	 * @return $contents string
	 * @param $filename string
	 * @param $title Title object
	 * @param $minify boolean[optional] removes newline
	 *
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

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// HOOK SERVICES to other extensions
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * Page loading service to other extensions
	 * NOTE that $prefix is only useful for pages located in the
	 *      MediaWiki database
	 * In priority order, this hook tries fetching the page from:
	 * 1- Parser Cache
	 * 2- MediaWiki article database 
	 * 3- PEAR repository under the MediaWiki/$name directory
	 * 4- MediaWiki installation directory i.e. $IP.'/extensions/'.$name
	 * 
	 * @param $prefix prefix of the page in case it is found in the database
	 * @param $name   name of page
	 * @param $result holds the result
	 */
	public function hpage_server( &$prefix, &$name, &$result, &$id )
	{
		// The calling party should place $result = NULL
		// and verify that $result !== NULL => extension present.

		$result = $this->fetch_page( $prefix, $name, $id );
		
		return true;	
	}	

	/**
	 * Fetches a page located remotely i.e. accessible through HTTP
	 * Caching is provided through MediaWiki's parser cache.
	 * NOTE that the parser cache is used to store the ''raw'' article
	 *      text without having being parsed. This behavior departs from
	 *      the normal MediaWiki one.
	 *      
	 * NOTE2 if parser caching isn't configured, performance will be
	 *       affected accordingly.
	 * 
	 * @return boolean
	 * @param $uri string base URI for HTTP accessible page
	 * @param $page string receives the page contents
	 * @param $etag string
	 * @param $source constant page's origin
	 * @param $state constant page's state
	 * @param $expiry integer expiry timeout
	 */
	public function hpage_remote(	&$uri, &$page, &$etag, 
									&$source, &$state, $expiry = null )
	{
		// prepare worst case
		$source      = self::SOURCE_ERROR;
		$state       = self::STATE_ERROR;
		
		$etag        = null;
		$page        = null;
		
		$cache_etag  = null;
		$remote_etag = null;
		$remote_page = null;
			
		// FLOW 1: cache HIT
		// verify if we have the page in the cache already
		// and it is not expired
		if ( $this->flow1( $uri, $page, $cache_etag ) )
		{
			$etag = $cache_etag;
			$source = self::SOURCE_CACHE;
			$state  = self::STATE_CACHE_OK;
			return true;
		}
		
		// Flag expired... 
		// Can we access the remote page?
		// If YES, update our cache and return it
		// NOTE that the page's etag is already embedded in the contents
		$remote_page = PageServer_Remote::getAndProcessRemotePage( $uri , $remote_etag );
		if ( is_string( $remote_page ))
		{
			$page = $remote_page;
			$this->flow2( $uri, $remote_page );
			$etag = $remote_etag;
			$source = self::SOURCE_REMOTE;
			$state  = self::STATE_CACHE_OK;
			return true;
		}
		// we can't access the remote page BUT
		// do we have a local cache copy of the page?
		elseif ( is_string( $page ) && !empty( $page ))
		{
			$source = self::SOURCE_CACHE;
			$state  = self::STATE_CACHE_STALE;
			$etag = $cache_etag;
			return true;
		}
		
		// from this point:
		// 1- no cache copy of the page
		// 2- nor is the remote page accessible
		// Worst case is already prepared ... just bail out.
		
		return true;
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// HOOK HELPERS
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * Check cache
	 * 
	 * @return 
	 */
	private function flow1( &$uri, &$page, &$etag )
	{
		$flag = $this->getFromCache( $uri, $page );
		$etag = PageServer_Remote::extractEtag( $page );
		return ( is_string( $flag ) );
	}
	private function flow2( &$uri, &$page )
	{
		return $this->saveInCache( $uri, $page );
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
	protected function fetch_page( &$prefix, &$name, &$id )
	{
		$result = null;
		
		$title = null;
		
		// first, let's try the parser cache
		$article = $this->buildArticle( $prefix, $name, $title );
		if ( is_object( $article ))
			if ( $article->getID() !=0 )
				$result = $this->fetchParserCache( $article, $id );	
				
		// next, let's try the database directly
		// happens when parser caching isn't available (unfortunately)				
		if ( !is_string( $result ))
			$result = $this->fetchDatabase( $prefix, $name, $id, $title );
				
		// next, try PEAR directory
		if ( !is_string( $result ))
			$result = $this->fetchPear( $name, $id );
		
		global $IP;
		// finally, try the /extensions directory
		if ( empty( $result ))
		{
			$path = $IP.'/extensions/'.$name;
			$id = @filemtime( $path );
			$result = @file_get_contents( $path );
		}
		
		return $result;
	}
	/**
	 * Fetchs a 'page' from the PEAR directory
	 * 
	 * @return $result string
	 * @param $name string
	 */
	protected function fetchPear( &$name, &$id )
	{
		$pearPath = $this->findPearPath();
		$path = $pearPath.'/'.self::$pear.'/'.$name;
		$id = @filemtime( $path ) ;
		return @file_get_contents( $path );
	}
	/**
	 * Builds a valid article object
	 * 
	 * @return $article Object
	 * @param $prefix string
	 * @param $name string
	 */
	protected function buildArticle( &$prefix, &$name, &$title )
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
	protected function fetchParserCache( &$article, &$id )
	{
		global $wgUser;
				
		$parserCache =& ParserCache::singleton();
		$po = $parserCache->get( $article, $wgUser );
		if ( is_object( $po ))
		{
			$id = $po->getCacheTime();
			return $po->getText();
		}
			
		return null;
	}
	/**
	 * Fetches a page directly from the database
	 * 
	 * @return $result string
	 * @param $prefix string
	 * @param $name string
	 */
	protected function fetchDatabase( &$prefix, &$name, &$id, &$title )
	{
		$contents = null;
		$rev = Revision::newFromTitle( $title );

		if( $rev )
		{
			$id = $rev->getId();
		    $contents = $rev->getText();		
		}

		if (is_string( $contents ) && !empty( $contents ))
		{
			global $wgRawHtml;

			// make sure we have raw html processing handy			
			$tmp = $wgRawHtml;
			$wgRawHtml = true;

			self::initParser();
			$po = self::$parser->parse( $contents, $title, new ParserOptions() );
			$contents = $po->getText();
			
			$wgRawHtml = $tmp;
		}

		return $contents;
	}
	
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// PARSER FUNCTIONS SERVICES
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
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// CACHE related
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	protected function saveInCache( &$name, &$contents, $expiry = null )
	{
		$cache = null;
		
		if ( !$this->verifyParserCache( $cache ) )
			return false;
			
		// default expiry
		if ( is_null( $expiry ))
			$expiry = self::$_expiry;
			
		$key_name = wfMemcKey( self::CACHE_PREFIX,      $name );
		$key_flag = wfMemcKey( self::CACHE_PREFIX_FLAG, $name );		
		
		// don't expiry the page's content, just the etag
		$cache->set( $key_name, $contents, 0 );
		
		// just a flag set to expire
		$cache->set( $key_flag, "flag",    $expiry );		
		
		return true;
	}
	/**
	 * Gets a page from the cache
	 * 
	 * @return $contents mixed string/boolean
	 * @param $name Object
	 */
	protected function getFromCache( &$name, &$contents )
	{
		$cache = null;
		
		if ( !$this->verifyParserCache( $cache ) )
			return false;
		
		$key_name = wfMemcKey( self::CACHE_PREFIX,      $name );
		$key_flag = wfMemcKey( self::CACHE_PREFIX_FLAG, $name );		

		$contents = $cache->get( $key_name );		
		return $cache->get( $key_flag );				
	}
	/**
	 * Verifies if parser caching is available
	 * 
	 * @return $result boolean
	 */	
	protected function verifyParserCache( &$cache = null )
	{
		$cache = wfGetParserCacheStorage();
		return ( $cache instanceof FakeMemCachedClient ) ? false:true ;
	}
	
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Special:Version helper
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	
	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		// Parser Caching in place?
		$result = "Parser Caching is ";
		$state  = $this->verifyParserCache() ? StubManager::STATE_OK: StubManager::STATE_ATTENTION;
		$result .= ( $state == StubManager::STATE_OK ) ? "available.": "<b>not available</b>.";
		StubManager::registerState( __CLASS__, $state );

		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result.'<br/>';
				
		return true; // continue hook-chain.
	}	
	
} // end class

//</source>
