<?php
/**
 * @author Jean-Lou Dupont
 * @package UserLoginLogoutLog
 * @version $Id$
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'UserLoginLogoutLog',
	'version' 		=> '1.0.0',
	'author'  		=> 'Jean-Lou Dupont',
	'description'	=> 'Provides logging of user login/logout activities.', 
	'url' 		=> 'http://mediawiki.org/wiki/Extension:UserLoginLogoutLog',			
);
StubManager::createStub(	'UserLoginLogoutLog', 
							dirname(__FILE__).'/UserLoginLogoutLog.body.php',
							dirname(__FILE__).'/UserLoginLogoutLog.i18n.php',							
							array(	'UserLoginForm', 'UserLoginComplete', 
									'UserLogout', 'UserLogoutComplete',
									'SpecialVersionExtensionTypes' ),
							true
						 );
//</source>
