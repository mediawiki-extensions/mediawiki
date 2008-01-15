<?php
/**
 * @author Jean-Lou Dupont
 * @package Ohloh
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:Ohloh]] <b>requires</b> [[Extension:StubManager]] version >= 1.1.0'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'Ohloh',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:Ohloh',	
		'description' 	=> "Provides integration with [http://www.ohloh.net Ohloh]", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'Ohloh', 
										'classfilename'	=> dirname(__FILE__).'/Ohloh.body.php',
										'mgs'			=> array( 'Ohloh' )
									)
							);
}
//</source>
