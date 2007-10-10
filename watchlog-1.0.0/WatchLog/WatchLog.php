<?php
/**
 * @author Jean-Lou Dupont
 * @package WatchLog
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    => 'WatchLog',
	'version' => '1.0.0',
	'author'  => 'Jean-Lou Dupont',
	'description' => 'Provides logging of user-to-user emailing activities', 
	'url'		=> 'http://mediawiki.org/wiki/Extension:WatchLog',
);
StubManager::createStub(	'WatchLog', 
							dirname(__FILE__).'/WatchLog.body.php',
							dirname(__FILE__).'/WatchLog.i18n.php',							
							array('WatchArticleComplete', 'UnwatchArticleComplete' ),
							true
						 );
//</source>
