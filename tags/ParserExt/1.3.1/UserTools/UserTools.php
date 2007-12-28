<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage UserTools
 * @version 1.3.1
 * @Id $Id: UserTools.php 762 2007-12-14 15:20:18Z jeanlou.dupont $
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
