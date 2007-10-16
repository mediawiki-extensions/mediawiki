<?php
/**
 * @author Jean-Lou Dupont
 * @package RegistryManager
 */
//<source lang=php>
class RegistryManager
{
	const base = 'Registry/';
	static $pattern_base = '/^Registry\/(.*)/si';
	static $thisType = 'other';
	static $thisName = 'RegistryManager';
	
	var $page;
	var $params;
	var $saving;
	
	// CACHE related
	static $realCache = true; // assume best case.
	static $cache = null;	
	static $expiryPeriod = 86400; // 1day.
	
	public function __construct()
	{
		$this->saving = false;
		$this->params = array();
		$this->page = false;
		self::initCacheSupport();
	}
	public function hArticleSave(	&$article, &$user, &$text,
									&$summary, $minor,
									$dc1, $dc2, &$flags )	
	{
		$full_titre = $article->mTitle->getDBkey();
		$result = $page = $this->extractPage( $full_titre );
		
		// bail out if we are not in the right sub-namespace
		if ($result === false)
			return true;

		// state variable to properly trigger
		// the 'RegistryPageChanged' event.
		$this->saving = true;
		
		// start collecting what we need to save
		// on a clean slate.
		$this->params = null;
		
		// Invoking the parser should get all the parameters
		// set on the page (through parser functions) collected
		// in this Registry object for storage.
		$this->parse( $article->mTitle, $text ); 
		
		// and finally write to cache.
		$this->writeToCache( $page, $this->params[$page] );
		
		return true;	
	}								
	/**
	 */
	public function hArticleSaveComplete(	&$article, 
											&$user, 
											&$text, 
											&$summary, 
											&$minoredit, 
											&$watchthis, 
											&$sectionanchor, 
											&$flags, 
											$revision)
	{
		// this hook is anyhow just triggered in the NS_MEDIAWIKI namespace
		// as per functionality from [[Extension:StubManager]]
		if ($this->saving)
			wfRunHooks( 'RegistryPageChange', array( &$this, &$this->page ) );
	
		return true;	
	}
	/**
	 */
	protected function extractPage( &$titre )
	{
		$result = preg_match( self::$pattern_base, $titre, $match );	
		if ($result == 0)
			return false;
		return $match[1];
	}
	/**
	 * This hooks should be called from an extension.
	 */
	public function hRegistryPageSet( &$page, &$key, &$value )
	{
		global $action;
		
		// only start recording when we are updating
		// the registry page.
		if ($action !== 'submit')
			return true;
			
		$this->params[$page][] = array( $key => $value );
		return true;
	}
	/**
	 */
	public function hRegistryPageGet( &$page, &$params )
	{
		$params = null;
		
		if (self::usingRealCache())
			$params = $this->params[$page] = self::readFromCache( $page );
		
		// if we get nothing from the cache,
		// try parsing the page.
		if (empty( $this->params[$page] ))
		{
			$result = $this->loadAndParse( $page );
			if ($result)
				$params = $this->params[$page];				
		}
			
		return true;			
	}

// ##########################################################################	
// ##########################################################################	
	
	/**
	
	 */
	protected function loadAndParse( $page )
	{
		$contents = null;
		$title = Title::newFromText( self::base.$page, NS_MEDIAWIKI );
		$rev = Revision::newFromTitle( $title );
		if( $rev )
		    $contents = $rev->getText();		

		if (empty( $contents ))
			return false;
		
		$this->parse( $title, $contents );
		
		return true;
	}

	/**
	 */
	protected function parse( &$title, &$text )	
	{
		global $wgParser, $wgUser;
		
		// clone the standard parser just to
		// make sure we don't break something.
		$parser = clone $wgParser;
		
		$popts = new ParserOptions( $wgUser );
		$parserOutput = $parser->parse(	$text, 
										$title, 
										$popts, 
										true, true, 
										null );
	}

	
// ##########################################################################	
// ##########################################################################	

	
	/**
	 * Builds a unique cache key
	 */
	static function getKey( /* args here*/ )
	{
		global $wgDBprefix, $wgDBname;
		$args = func_get_args();
		if ( $wgDBprefix ) {
			$key = "$wgDBname-$wgDBprefix:" . implode( ':', $args );
		} else {
			$key = $wgDBname . ':' . implode( ':', $args );
		}
		return $key;
	}
	static function initCacheSupport()
	{
		self::$cache = & wfGetMainCache();	

		if (self::$cache instanceof FakeMemCachedClient)
			self::$realCache = false;
	}

	/**
	 */
	static function writeToCache( &$page, &$data )
	{
		if (!self::$realCache)
			return false;
			
		$key = self::getKey( 'Registry/'.$page );
			
		$s = serialize( $data );
		
		self::$cache->set( $key, $s, self::$expiryPeriod );
	}
	/**
	 */
	static function readFromCache( &$page )
	{
		if (!self::$realCache)
			return false;

		$key = self::getKey( 'Registry/'.$page );
				
		$s = self::$cache->get( $key );

		return @unserialize( $s );
	}
	/**
	 */
	static function usingRealCache()
	{
		return self::$realCache;	
	}


// ##########################################################################	
// ##########################################################################	

	
	/**
	 */
	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		$result1 = ' Using caching: ';
		$result1 .= self::$realCache ? "true.":"<b".">false<"."/b>.";
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1;
				
		return true; // continue hook-chain.
	}
	
	
	
} // end class declaration
//</source>