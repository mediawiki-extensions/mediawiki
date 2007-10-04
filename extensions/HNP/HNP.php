<?php
/**
 * @author Jean-Lou Dupont
 * @package HNP
 */
//<source lang=php>
require 'HNP.i18n.php';

if (class_exists('StubManager'))
{
	StubManager::createStub2(	array(	'class' 		=> 'HNP', 
										'classfilename'	=> dirname(__FILE__).'/HNP.body.php',
										'hooks'			=> array( 'userCan', 'UserIsAllowed' ),
										'mgs'			=> array( 'hnp', 'hnp_r' )
									)
							);

	$wgExtensionCredits['hook'][] = array( 
		'name'    		=> 'HNP',
		'version'		=> StubManager::getRevisionId('$Id$'),
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:HierarchicalNamespacePermissions2',	
		'description' 	=> "Provides enhancements to the permission management sub-system.", 
	);
}
else
	echo 'Extension:HNP <b>requires</b> [[Extension:StubManager]]'."\n";
//</source>