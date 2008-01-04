<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitRating
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:JSKitRating]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'JSKitRating',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:JSKitRating',	
		'description' 	=> "Provides integration with JSKit Rating tool.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'JSKitRating', 
										'classfilename'	=> dirname(__FILE__).'/JSKitRating.body.php',
										'mgs'			=> array( 'jskitrating' )
									)
							);
}
//</source>
