<?php
/**
 * @author Jean-Lou Dupont
 * @package UserAccountManager
 * @version $Id$
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        => 'UserAccountManager', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Manages User Account creation",
	'url'		=> 'http://mediawiki.org/wiki/Extension:UserAccountManager',
);
StubManager::createStub2(	array(	'class' 		=> 'UserAccountManager', 
									'classfilename'	=> dirname(__FILE__).'/UserAccountManager.body.php',
									'hooks'			=> array(	'AddNewAccount',
															),
								)
						);
//</source>