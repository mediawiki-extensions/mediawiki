<?php
/**
 * @author Jean-Lou Dupont
 * @package RegistryManager
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'        => 'RegistryManager', 
		'version'     => '@@package-version@@',
		'author'      => 'Jean-Lou Dupont', 
		'description' => "Manages a registry for extensions' usage",
		'url'		=> 'http://mediawiki.org/wiki/Extension:RegistryManager',
	);
	StubManager::createStub2(	array(	'class' 		=> 'RegistryManager', 
										'classfilename'	=> dirname(__FILE__).'/RegistryManager.body.php',
										'hooks'			=> array(	'ArticleSave',
																	'ArticleSaveComplete',
																	'SpecialVersionExtensionTypes',
																	// Created by this extension:
																	'RegistryPageGet',
																	'RegistryPageSet',
																	'RegistryPageChanged'
																),
									)
							);
}
else
	echo "Extension:RegistryManager requires Extension:StubManager.";						
//</source>