<?php
/**
 * @author Jean-Lou Dupont
 * @package GroupManager
 * @version $Id$
 */
//<source lang=php>
if (class_exists('StubManager'))
{
	StubManager::createStub2(	
		array(	'class' 		=> 'GroupManager', 
				'classfilename'	=> dirname(__FILE__).'/GroupManager.body.php',
				'hooks'			=> array(	
											'SpecialVersionExtensionTypes',
											'UserEffectiveGroups',
											//'EditFormPreloadText'
										),
				'mgs'			=> array( 'wggroup' )
				)
	);

	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'GroupManager',
		'version'		=> '1.0.2',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:GroupManager',	
		'description' 	=> "Provides group definition management.", 
	);
}
else
	echo 'Extension:GroupManager <b>requires</b> [[Extension:StubManager]]'."\n";

//</source>