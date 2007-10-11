<?php
/**
 * @author Jean-Lou Dupont
 * @package DocProc
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'DocProc', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Documents wikitext with 'markup/magic words' whilst still processing as per normal.",
	'url' 		=> 'http://mediawiki.org/wiki/Extension:DocProc',			
);

StubManager::createStub(	'DocProc', 
							dirname(__FILE__).'/DocProc.body.php',
							null,							
							null,
							false, 					// no need for logging support
							array('docproc'),		// tags
							null, 					// parser Functions
							null
						 );
//</source>
