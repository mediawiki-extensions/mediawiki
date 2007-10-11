<?php
/**
 * @author Jean-Lou Dupont
 * @package EmailLog
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    => 'EmailLog',
	'version' => '1.0.0',
	'author'  => 'Jean-Lou Dupont',
	'description' => 'Provides logging of user-to-user emailing activities',
	'url'		=> 'http://mediawiki.org/wiki/Extension:EmailLog',
);
StubManager::createStub(	'EmailLog', 
							dirname(__FILE__).'/EmailLog.body.php',
							dirname(__FILE__).'/EmailLog.i18n.php',							
							array('EmailUserComplete'),
							true
						 );
//</source>
