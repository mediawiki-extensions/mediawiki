<?php
/**
 * @author Jean-Lou Dupont
 * @package UserTools
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'UserTools', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => ' ',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:UserTools',						
);
StubManager::createStub2(	array(	'class' 		=> 'UserTools', 
									'classfilename'	=> dirname(__FILE__).'/UserTools.body.php',
									'mgs'			=> array(	'cusergetoption', 
																'usergetoption' ),
								)
						);
//</source>
