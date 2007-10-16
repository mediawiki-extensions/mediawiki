<?php
/**
 * @author Jean-Lou Dupont
 * @package RegistryManager
 * @version $Id$
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        => 'RegistryManager', 
	'version'     => '1.0.0',
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
//</source>