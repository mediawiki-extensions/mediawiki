<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage UserTools
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        => 'UserTools', 
	'version'     => '1.1.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'User related parser functions',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:UserTools',						
);
StubManager::createStub2(	array(	'class' 		=> 'UserTools', 
									'classfilename'	=> dirname(__FILE__).'/UserTools.body.php',
									'mgs'			=> array(	'cusergetoption', 
																'usergetoption',
																'cuserfromgroup',
																'userfromgroup'
																),
								)
						);
//</source>
