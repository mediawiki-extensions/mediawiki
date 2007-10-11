<?php
/**
 * @author Jean-Lou Dupont
 * @package GoogleCode
 * @version $Id$
*/
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    	=> 'GoogleCode',
	'version' 	=> '1.0.0',
	'author'  	=> 'Jean-Lou Dupont',
	'description' => "Secure syntax highlighting of source code found on GoogleCode SVN", 
	'url' 		=> 'http://mediawiki.org/wiki/Extension:GoogleCode',	
);
StubManager::createStub2(	array(	'class' 		=> 'GoogleCode', 
									'classfilename'	=> dirname(__FILE__).'/GoogleCode.body.php',
									'tags'			=> array( 'gcode' ),
									'mgs'			=> array( 'gcode' )
								)
						);
//</source>