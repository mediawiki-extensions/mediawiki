<?php
/**
 * @author Jean-Lou Dupont
 * @package ToolboxExtender	
 * @version $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:ToolboxExtender]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'ToolboxExtender',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:ShareIt',	
		'description' 	=> "Provides adding arbitrary wikitext to the toolbox area. ToolboxExtender page is [[MediaWiki:Registry/ToolboxExtender]].", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'ToolboxExtender', 
										'classfilename'	=> dirname(__FILE__).'/ToolboxExtender.body.php',
										'hooks'			=> array( 'MonoBookTemplateToolboxEnd' ),
									)
							);
}
//</source>