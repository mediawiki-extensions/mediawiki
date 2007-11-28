<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureTransclusion
 * @version 1.1.0
 * @Id $Id: SecureTransclusion.body.php 686 2007-11-09 15:14:29Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:SecureTransclusion]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SecureTransclusion',
		'version'		=> '1.1.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SecureTransclusion',	
		'description' 	=> "Provides secure interwiki transclusion.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SecureTransclusion', 
										'classfilename'	=> dirname(__FILE__).'/SecureTransclusion.body.php',
										'mgs'			=> array( 'strans' )
									)
							);
}
//</source>
