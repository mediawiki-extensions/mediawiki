<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage MiscParserFunctions
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'MiscParserFunctions', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Miscellaneous parser functionality',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:MiscParserFunctions',			
);

StubManager::createStub(	'MiscParserFunctions', 
							dirname(__FILE__).'/MiscParserFunctions.body.php',
							null,							
							null,
							false, // no need for logging support
							null,	// tags
							array( 'trim','nowikitext','gettagsection' ),  //of parser function magic words,
							null
						 );
//</source>
