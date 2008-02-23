<?php
/**
 * @author Jean-Lou Dupont
 * @package ProgressBar
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:ProgressBar]] <b>requires</b> [[Extension:StubManager]] version >= 1.1.0'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'ProgressBar',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:ProgressBar',	
		'description' 	=> "Provides customizable progress bars.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'ProgressBar', 
										'classfilename'	=> dirname(__FILE__).'/ProgressBar.body.php',
										'mgs'			=> array( 'progressbar' )
									)
							);
}
//</source>
