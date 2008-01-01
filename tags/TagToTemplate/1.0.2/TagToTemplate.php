<?php
/**
 * @author Jean-Lou Dupont
 * @package TagToTemplate
 * @version 1.0.2
 * @Id $Id: TagToTemplate.php 797 2008-01-01 21:08:35Z JeanLou.dupont $ 
 */
// <source lang=php>
if (class_exists('StubManager'))
{
	StubManager::createStub2(	array(	'class' 		=> 'TagToTemplate', 
										'classfilename'	=> dirname(__FILE__).'/TagToTemplate.body.php',
										'hooks'			=> array( 'ParserBeforeStrip' ),
										//'mgs'			=> array( 'tag_to_template' )
									)
							);
	global $wgExtensionCredits;
	$wgExtensionCredits['hook'][] = array( 
		'name'    		=> 'TagToTemplate',
		'version'		=> '1.0.2',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:TagToTemplate',	
		'description' 	=> "Provides tag markup substitution for a configured template.", 
	);
}
else
	echo 'Extension:TagToTemplate <b>requires</b> [[Extension:StubManager]]'."\n";
//</source>