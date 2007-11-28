<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureTransclusion
 * @version 1.1.2
 * @Id $Id: SecureTransclusion.php 703 2007-11-28 02:15:33Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:SecureTransclusion]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SecureTransclusion',
		'version'		=> '1.1.2',
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
