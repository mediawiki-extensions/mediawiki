<?php
/**
 * @author Jean-Lou Dupont
 * @package DirectoryManager
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    => 'DirectoryManager',
	'version' => '1.0.0',
	'author'  => 'Jean-Lou Dupont',
	'description' => "Provides a namespace 'Directory' for browsing the filesystem of a MediaWiki installation.", 
	'url' 		=> 'http://mediawiki.org/wiki/Extension:DirectoryManager',	
);

StubManager::createStub2(	array(	'class' 		=> 'DirectoryManager', 
									'classfilename'	=> dirname(__FILE__).'/DirectoryManager.body.php',
									'hooks'			=> array(	'ArticleFromTitle',
																'CustomEditor',
															), //end hooks
									'nss'			=>	array(NS_DIRECTORY),
									'mgs'			=>	array( 'directory' ),
								)
						);
//</source>
