<?php
/**
 * @author Jean-Lou Dupont
 * @package WatchRight
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'WatchRight', 
	'version'		=> '1.0.0',
	'author'		=> 'Jean-Lou Dupont', 
	'description'	=> "Enforces 'watch/unwatch' rights",
	'url' 			=> 'http://mediawiki.org/wiki/Extension:WatchRight',			
);

StubManager::createStub(	'WatchRight', 
							dirname(__FILE__).'/WatchRight.body.php',
							null,
							array( 'WatchArticle','UnwatchArticle','SkinTemplateTabs' ),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null	// no magic words
						 );
//</source>
