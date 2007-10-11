<?php
/**
 * @author Jean-Lou Dupont
 * @package FileManager
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'FileManager', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Manages the files in a Mediawiki installation. Namespace for filesystem is ',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:FileManager',
);
StubManager::createStub2(
				array(	'class' 		=> 'FileManager', 
						'classfilename' => dirname(__FILE__).'/FileManager.body.php',
						'i18nfilename'	=> dirname(__FILE__).'/FileManager.i18n.php',
						'logging'		=> true,
						'hooks'			=> array( 'ArticleSave',
												'ArticleFromTitle',
												'EditFormPreloadText', 
												'OutputPageBeforeHTML', 
												'SkinTemplateTabs', 
												'UnknownAction',
												'SpecialVersionExtensionTypes' ),
						'nss'			=> array( NS_FILESYSTEM ),
						'mgs'			=> array(	'extractmtime', 
													'extractfile', 
													'comparemtime', 
													'currentmtime' ),
						) );
//</source>