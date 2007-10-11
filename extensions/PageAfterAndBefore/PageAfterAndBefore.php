<?php
/**
 * @author Jean-Lou Dupont
 * @package PageAfterAndBefore
 * @version $Id$
*/
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'		=> 'PageAfterAndBefore',
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Provides a 'magic word' interface to retrieve 'preceeding' and 'succeeding' pages relative to a given page title.",
	'url' 		=> 'http://mediawiki.org/wiki/Extension:PageAfterAndBefore',						
);

StubManager::createStub(	'PageAfterAndBefore', 
							dirname(__FILE__).'/PageAfterAndBefore.body.php',
							null,					// i18n file			
							null,					// hooks
							false, 					// no need for logging support
							null,					// tags
							array('pagebefore', 'pageafter', 'firstpage', 'lastpage' ),	// parser Functions
							null
						 );
//</source>
