<?php
/**
 * @author Jean-Lou Dupont
 * @package Quimble
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:Quimble]] <b>requires</b> [[Extension:StubManager]] version >= 1.1.0'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'Quimble',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:Quimble',	
		'description' 	=> "Provides integration with [http://quimble.com Quimble]", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'Quimble', 
										'classfilename'	=> dirname(__FILE__).'/Quimble.body.php',
										'mgs'			=> array( 'quimble_poll' )
									)
							);
}
//</source>
