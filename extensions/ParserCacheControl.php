<?php
/*
 * ParserCacheControl.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose: Controls the Parser Cache without having to
 *          patch a Mediawiki installation.
 *
 * Benefit: Simplify development of complex dynamical extensions whilst
 *          preserving the advantages of parser output caching. 
 *
 * Features:
 * *********
 * - Parser Cache disabling upon article updating/creation (default behavior)
 *
 * DEPENDANCIES:
 * - ExtensionClass (>v1.3)
 *
 * Tested Compatibility:  MW 1.8.2, 1.9.3
 *
 * HISTORY:
 * -- Version 1.0
 */

ParserCacheControl::singleton();

class ParserCacheControl extends ExtensionClass
{
	const thisName = 'ParserCacheControl';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version

	// parser cache control parameter
	// This property is public and thus can be manipulated
	// by other extensions
	var $disableParserCache;
	
	public static function &singleton( )
	{ return parent::singleton( ); }
	
	// Our class defines magic words: tell it to our helper class.
	public function ParserCacheControl() 
	{
		parent::__construct( );
		global $wgExtensionCredits, $wgParserCacheType, $wgEnableParserCache, $parserMemc;

		$pct = array( CACHE_NONE => "CACHE_NONE", CACHE_ANYTHING => "CACHE_ANYTHING",
					CACHE_MEMCACHED => "CACHE_MEMCACHED", CACHE_DB => "CACHE_DB",
					CACHE_ACCEL => "CACHE_ACCEL", CACHE_DBA => "CACHE_DBA",
		);

		// Let's provide some helpful information in the
		// 'Special:Version' page.
		$wgExtensionCredits[self::thisType][] = array( 
			'name'    => self::thisName, 
			'version' => '$LastChangedRevision$',
			'author'  => 'Jean-Lou Dupont', 
			'url'     => 'http://www.bluecortex.com',
			'description' => "Parser Cache (\$wgEnableParserCache) is currently <b>".($wgEnableParserCache ? "enabled":"disabled").
			                 "</b> whilst \$wgParserCacheType is set to <b>".$pct[$wgParserCacheType]."</b>",
		);
		
		// by default, parser cache is now disabled
		// when Article::editupdates is performed.
		$this->disableParserCache = true;
	}
	public function setup()
	{
		parent::setup();
		
		global $wgHooks;
		$wgHooks['ArticleSave'][] =                  array( &$this, 'hArticleSave' );	
	}
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits, $parserMemc;
		
		$pmc = get_class( $parserMemc );
			
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
		{
			if ($el['name']==self::thisName)
				$el['description'].=" and \$parserMemc is set to a <b>{$pmc}</b> object";	
		}
	}
	public function hArticleSave( &$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags )
	{
		if (!$this->disableParserCache)
			return true;
			
		global $wgParserCacheType;
		// disable the parser cache for this transaction.
		// The hack below will affect the method Article::editUpdates
		// into not saving the current article to a potential real cache.
		// BUT, once the article is viewed, the article will then be stored in the real cache.	
		$wgParserCacheType = CACHE_NONE;
		$apc =& wfGetParserCacheStorage();
		
		$pc = & ParserCache::singleton();
		$pc->mMemc = $apc;

		return true;
	}

} // end class definition.
?>