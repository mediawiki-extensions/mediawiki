<?php
/**
 * @author Jean-Lou Dupont
 * @package AddScriptCss
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'        => 'AddScriptCss', 
	'version'     => StubManager::getRevisionId( '$Id$' ),
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Adds javascript and css scripts to the page HEAD or BODY sections',
	'url'		=> 'http://mediawiki.org/wiki/Extension:AddScriptCss',
);

StubManager::createStub(	'AddScriptCss', 
							dirname(__FILE__).'/AddScriptCss.body.php',
							null,							
							array( 'OutputPageBeforeHTML', 'ParserAfterTidy' ),
							false, 								// no need for logging support
							array( 'addtohead', 'addscript' ),	// tags
							array( 'addscript' ), 				//of parser function magic words,
							null
						 );
//</source>