<?php
/**
 * @author Jean-Lou Dupont
 * @package EmbedObject
 * @version 1.0.0
 * @Id $Id$
*/
//<source lang=php>
if ( class_exists('StubManager') )
{
	$wgExtensionCredits['other'][] = array( 
		'name'    	=> 'EmbedObject',
		'version' 	=> '1.0.0',
		'author'  	=> 'Jean-Lou Dupont',
		'description' => "Provides object embedding capability", 
		'url' 		=> 'http://mediawiki.org/wiki/Extension:EmbedObject',	
	);
	StubManager::createStub2(	array(	'class' 		=> 'EmbedObject', 
										'classfilename'	=> dirname(__FILE__).'/EmbedObject.body.php',
										'mgs'			=> array( 'embed' )
									)
							);
}
else
	echo 'Extension:EmbedObject requires Extension:StubManager';					
//</source>