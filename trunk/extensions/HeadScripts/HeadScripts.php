<?php
/**
 * @author Jean-Lou Dupont
 * @package HeadScripts
 * @version $Id$
 */
//<source lang=php>
if (class_exists('StubManager'))
{
	StubManager::createStub2(	
		array(	'class' 		=> 'HeadScripts', 
				'classfilename'	=> dirname(__FILE__).'/HeadScripts.body.php',
				'hooks'			=> array(	
											'SpecialVersionExtensionTypes',
											'BeforePageDisplay',
										),
				'mgs'			=> array( 'headscript' )
				)
	);

	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'HeadScripts',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:HeadScripts',	
		'description' 	=> "Provides the facility to add HEAD scripts from a secure page.", 
	);
}
else
	echo 'Extension:HeadScripts <b>requires</b> [[Extension:StubManager]]'."\n";

//</source>
//</source>