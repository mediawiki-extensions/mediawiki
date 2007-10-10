<?php
/**
 * @author Jean-Lou Dupont
 * @package PermissionFunctions
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
