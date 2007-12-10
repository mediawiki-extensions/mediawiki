<?php
/**
 * @author Jean-Lou Dupont
 * @package EmbedObject
 * @version @@package-version@@
 * @Id $Id$
*/
//<source lang=php>
if ( class_exists('StubManager') )
{
	$wgExtensionCredits['other'][] = array( 
		'name'    	=> 'EmbedObject',
		'version' 	=> '@@package-version@@',
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