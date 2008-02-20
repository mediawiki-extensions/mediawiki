<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage PermissionFunctions
 * @version 1.3.2
 * @Id $Id: PermissionFunctions.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'PermissionFunctions', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Provides a collection of permission management functionality.',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:PermissionFunctions',			
);
StubManager::createStub(	'PermissionFunctions', 
							dirname(__FILE__).'/PermissionFunctions.body.php',
							null,							
							array('EndParserPhase2'),
							false, // no need for logging support
							null,	// tags
							array( 'checkpermission' ),  //of parser function magic words,
							null
						 );
//</source>
