<?php
/**
 * @author Jean-Lou Dupont
 * @package PageToFile
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (defined('NS_PAGEFILE') && class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'        => 'PageToFile', 
		'version'     => '1.0.0',
		'author'      => 'Jean-Lou Dupont', 
		'description' => 'Provides page content transfer to file',
		'url' 		=> 'http://mediawiki.org/wiki/Extension:PageToFile',
	);
	StubManager::createStub2(
					array(	'class' 		=> 'FileManager', 
							'classfilename' => dirname(__FILE__).'/PageToFile.body.php',
							'i18nfilename'	=> dirname(__FILE__).'/PageToFile.i18n.php',
							'logging'		=> true,
							'hooks'			=> array( 'ArticleSave',
													),
							'nss'			=> array( NS_PAGEFILE ),
							) );
}
else
{
	echo 'Extension:PageToFile requires Extension:StubManager and NS_PAGEFILE namespace definition';
}				
//</source>