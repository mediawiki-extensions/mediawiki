<?php
/**
 * @author Jean-Lou Dupont
 * @package EmailLog
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'    => 'EmailLog',
		'version' => '@@package-version@@',
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
}
else
	echo "Extension:EmailLog requires Extension:StubManager";						 
//</source>