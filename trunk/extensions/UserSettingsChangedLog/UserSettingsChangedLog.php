<?php
/**
 * @author Jean-Lou Dupont
 * @package UserSettingsChangedLog
 * @version $Id$
 */
//<source lang=php>*/

$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'UserSettingsChangedLog',
	'version' 		=> StubManager::getRevisionId('$Id$'),
	'author'  		=> 'Jean-Lou Dupont',
	'description'	=> 'Provides logging of user settings changed', 
	'url' 		=> 'http://mediawiki.org/wiki/Extension:UserSettingsChangedLogging',			
);

StubManager::createStub(	'UserSettingsChangedLog', 
							dirname(__FILE__).'/UserSettingsChangedLog.body.php',
							dirname(__FILE__).'/UserSettingsChangedLog.i18n.php',							
							array('UserSettingsChanged'),
							true
						 );
//</source>
