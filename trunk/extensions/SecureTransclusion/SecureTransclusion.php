<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureTransclusion
 * @version $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:SecureTransclusion]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SecureTransclusion',
		'version'		=> '1.0.1',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SecureTransclusion',	
		'description' 	=> "Provides secure interwiki transclusion.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SecureTransclusion', 
										'classfilename'	=> dirname(__FILE__).'/SecureTransclusion.body.php',
										#'hooks'			=> array( 'ParserAfterTidy' ),
										'mgs'			=> array( 'strans' )
									)
							);
}
//</source>
