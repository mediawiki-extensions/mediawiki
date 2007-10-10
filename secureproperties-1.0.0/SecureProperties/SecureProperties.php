<?php
/**
	@author Jean-Lou Dupont
	@package SecureProperties
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits[ 'other' ][] = array( 
	'name'        => 'SecureProperties', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Enables global object property get/set on protected pages',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:SecureProperties',
);
StubManager::createStub(	'SecureProperties', 
							dirname(__FILE__).'/SecureProperties.body.php',
							null,	// no i18n
							null, 	// no hooks
							false,	// no need for logging support
							null,	// tags
							array( 'pg', 'ps', 'pf', 'gg', 'gs', 'cg', 'cs' ),
							null,	// no magic words
							null	// no namespace triggering
						 );
//</source>
