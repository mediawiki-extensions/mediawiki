<?php
/**
 * @author Jean-Lou Dupont
 * @package HTMLinMediaWikiNamespace
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        	=> 'HTMLinMediaWikiNamespace', 
	'version'     	=> '@@package-version@@',
	'author'      	=> 'Jean-Lou Dupont', 
	'description' 	=> 'Provides unrestricted HTML in the MediaWiki namespace',
	'url' 			=> 'http://mediawiki.org/wiki/Extension:HTMLinMediaWikiNamespace',			
);

class HTMLinMediaWikiNamespace
{
	static $wgRawHtml;
	
	public static function init()
	{
		global $wgExtensionFunctions;		
		$wgExtensionFunctions[] = array( 'HTMLinMediaWikiNamespace', 'run' );
	}
	public static function run()
	{
		global $wgHooks;
		$wgHooks[][] = array( 'HTMLinMediaWikiNamespace', 'hParserBeforeStrip' );
		$wgHooks[][] = array( 'HTMLinMediaWikiNamespace', 'hParserAfterStrip' );		
	}
	public static function hParserBeforeStrip( &$parser, &$text, &$mStripState )
	{
		$ns = $parser->mTitle->getNamespace();
		if ( $ns === NS_MEDIAWIKI )
		{
			global $wgRawHtml;
			$wgRawHtml = true;
		}
		return true;
	}
	public static function hParserAfterStrip( &$parser, &$text, &$mStripState )
	{
			
	}
}
HTMLinMediaWikiNamespace::init();
//</source>