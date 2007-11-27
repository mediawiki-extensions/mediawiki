<?php
/**
 * @author Jean-Lou Dupont
 * @package ToolboxExtender
 * @version 1.0.1
 * @Id $Id: ToolboxExtender.php 697 2007-11-27 12:25:53Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:ToolboxExtender]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'ToolboxExtender',
		'version'		=> '1.0.1',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:ToolboxExtender',	
		'description' 	=> "Provides adding arbitrary wikitext to the toolbox area. ToolboxExtender page is [[MediaWiki:Registry/ToolboxExtender]].", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'ToolboxExtender', 
										'classfilename'	=> dirname(__FILE__).'/ToolboxExtender.body.php',
										'hooks'			=> array( 'MonoBookTemplateToolboxEnd' ),
									)
							);
}
//</source>