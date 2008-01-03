<?php
/**
 * @author Jean-Lou Dupont
 * @package PageAfterAndBefore
 * @version 1.0.7
 * @Id $Id: PageAfterAndBefore.php 784 2007-12-22 00:39:11Z jeanlou.dupont $
*/
//<source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'		=> 'PageAfterAndBefore',
		'version'     => '1.0.7',
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
}
else
	echo 'Extension:PageAfterAndBefore requires Extension:StubManager';						 
//</source>
