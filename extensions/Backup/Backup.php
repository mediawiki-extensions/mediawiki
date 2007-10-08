<?php
/**
 * @author Jean-Lou Dupont
 * @package Backup
 */
// <source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'Backup',
	'version'		=> StubManager::getRevisionId('$Id$'),
	'author'		=> 'Jean-Lou Dupont',
	'url'			=> 'http://www.mediawiki.org/wiki/Extension:Backup',	
	'description' 	=> "Provides the 'backup' hook.", 
);

if (class_exists('StubManager'))
{
$backupExt = array();

// Exclude the following namespaces by default.
if (defined('NS_FILESYSTEM'))	$backupExt[] = NS_FILESYSTEM;
if (defined('NS_DIRECTORY'))	$backupExt[] = NS_DIRECTORY;

StubManager::createStub2(	array(	'class' 		=> 'Backup', 
									'classfilename'	=> dirname(__FILE__).'/Backup.body.php',
									'hooks'			=> array(	'RecentChange_save',
																'ArticleSaveComplete',
																'ArticleDeleteComplete',
																'ArticleDelete',
																'SpecialMovepageAfterMove',
																'ArticleProtectComplete',
																'ImageDoDeleteBegin',  // supported through [[Extension:ImagePageEx]]
															),
									// exclude the following namespaces
									'enss'			=> $backupExt
								)
						);
}
else
{
	echo "Extension:Backup requires Extension:StubManager";
}
//</source>
