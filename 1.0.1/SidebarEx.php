<?php
/**
 * @author Jean-Lou Dupont
 * @package SidebarEx	
 * @version $Id: SidebarEx.php 525 2007-10-11 22:23:38Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:SidebarEx]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SidebarEx',
		'version'		=> '1.0.1',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SidebarEx',	
		'description' 	=> "Provides customizable sidebars.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SidebarEx', 
										'classfilename'	=> dirname(__FILE__).'/SidebarEx.body.php',
										'hooks'			=> array( 'SkinTemplateOutputPageBeforeExec' ),
									)
							);
}
// </source>