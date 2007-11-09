<?php
/**
 * @author Jean-Lou Dupont
 * @package SimpleScript
 * @version $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:SimpleScript]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SimpleScript',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SimpleScript',	
		'description' 	=> "Provides a simple way of generating controlled javascript script tags.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SimpleScript', 
										'classfilename'	=> dirname(__FILE__).'/SimpleScript.body.php',
										'mgs'			=> array( 'sscript' )
									)
							);
}
//</source>
