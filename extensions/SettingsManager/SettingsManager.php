<?php
/**
 * @author Jean-Lou Dupont
 * @package SettingsManager
 * @version @@package-version@@
 * @Id $Id$
 */
// <source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SettingsManager',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SettingsManager',	
		'description' 	=> "Provides wikitext access to parameters of LocalSettings.php. "
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SettingsManager', 
										'classfilename'	=> dirname(__FILE__).'/SettingsManager.body.php',
										'i18nfilename'	=> dirname(__FILE__).'/SettingsManager.i18n.php',
										'logging'		=> true, 
										'hooks'			=> array( 'ParserAfterTidy' ),
										'nss'			=> array( NS_MEDIAWIKI ),
										'mgs'			=> array( 'setting' )
									)
							);
	
}
else
	echo "Extension:SettingsManager <b>requires</b> Extension:StubManager\n";

include( dirname(__FILE__).'/SettingsManager.settings.php' );
	
//</source>