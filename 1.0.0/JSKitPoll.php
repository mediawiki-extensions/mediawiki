<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitPoll
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:JSKitPoll]] <b>requires</b> [[Extension:StubManager]] version >= 1.1.0'."\n";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'JSKitPoll',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:JSKitPoll',	
		'description' 	=> "Provides integration with JSKit Polling tool.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'JSKitPoll', 
										'classfilename'	=> dirname(__FILE__).'/JSKitPoll.body.php',
										'mgs'			=> array( 'jskitpoll' )
									)
							);
}
//</source>
