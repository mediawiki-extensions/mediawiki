<?php
/**
 * @author Jean-Lou Dupont
 * @package NewUserEmailNotification.php
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'NewUserEmailNotification',
	'version' 		=> '1.0.0',
	'author'  		=> 'Jean-Lou Dupont',
	'description'	=> 'Provides email notification of new user account creation', 
	'url'			=> 'http://mediawiki.org/wiki/Extension:NewUserEmailNotification',				
);
StubManager::createStub(	'NewUserEmailNotification', 
							dirname(__FILE__).'/NewUserEmailNotification.body.php',
							dirname(__FILE__).'/NewUserEmailNotification.i18n.php',							
							array('AddNewAccount'),
							false
						 );
//</source>
