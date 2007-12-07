<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage PermissionFunctions
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'RegexTools', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Provides 'magic words' performing regular expression pattern ( aka 'regex' ) matching.",
	'url' 		=> 'http://mediawiki.org/wiki/Extension:RegexTools',			
);
StubManager::createStub(	'RegexTools', 
							dirname(__FILE__).'/RegexTools.body.php',
							null,							
							null,
							false, 						// no need for logging support
							null,						// tags
							array('regx_vars', 'regx'), // parser Functions
							null
						 );
//</source>
