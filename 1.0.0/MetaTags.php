<?php
/**
 * @author Jean-Lou Dupont
 * @package MetaTags
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:MetaTags]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'MetaTags',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:MetaTags',	
		'description' 	=> "Provides META tags to HEAD whilst integrating with parser caching.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'MetaTags', 
										'classfilename'	=> dirname(__FILE__).'/MetaTags.body.php',
										'mgs'			=> array( 'meta' )
									)
							);
}
//</source>
