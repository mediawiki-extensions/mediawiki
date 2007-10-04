<?php
/**
	@author Jean-Lou Dupont
	@package SecurePHP
 */
if (!class_exists('StubManager'))
	echo '[[Extension:SecurePHP]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SecurePHP',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SecurePHP',	
		'description' 	=> "Provides secure PHP code tag section.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SecurePHP', 
										'classfilename'	=> dirname(__FILE__).'/SecurePHP.php',
										'tags'			=> array( 'runphp' )
									)
							);
}
//</source>
