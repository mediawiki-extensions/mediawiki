<?php
/**
 * @author Jean-Lou Dupont
 * @package PageFunctions
 * @version $Id$
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'PageFunctions', 
	'version'     => '1.1.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Provides page scope functions',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:PageFunctions',						
);
StubManager::createStub(	'PageFunctions', 
							dirname(__FILE__).'/PageFunctions.body.php',
							null,
							array( 'PageVarGet', 'PageVarSet', 
									'OutputPageBeforeHTML' #for page title clear functionality
									),
							false, // no need for logging support
							null,	// tags
							array( 'pagetitle','pagesubtitle','pageexists',
									'varset', 'varget',
									'varaset', 'varaget',
									'varcapset',
									'cshow',
									'noext'
									 ),  				//of parser function magic words,
							array( 'noclientcaching' )	// magic words
						 );
//</source>
