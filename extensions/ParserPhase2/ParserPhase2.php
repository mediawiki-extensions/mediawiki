<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserPhase2
 * @version $Id$
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'        => 'ParserPhase2', 
	'version'     => '1.0.1',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Enables performing a 'second pass' parsing over an already cached page for replacing dynamic variables",
	'url' 		=> 'http://mediawiki.org/wiki/Extension:ParserPhase2',			
);

StubManager::createStub(	'ParserPhase2', 
							dirname(__FILE__).'/ParserPhase2.body.php',
							null,
							array( 'OutputPageBeforeHTML','ParserAfterTidy','ParserBeforeStrip',
								),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null	// no magic words
						 );
//</source>