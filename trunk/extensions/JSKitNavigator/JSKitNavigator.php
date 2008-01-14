<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitNavigator
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:JSKitNavigator]] <b>requires</b> [[Extension:StubManager]] version >= 1.1.0'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'JSKitNavigator',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:JSKitNavigator',	
		'description' 	=> "Provides integration with JSKit Navigator tool.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'JSKitNavigator', 
										'classfilename'	=> dirname(__FILE__).'/JSKitNavigator.body.php',
										'mgs'			=> array( 'jskitnavigator' )
									)
							);
}
//</source>
