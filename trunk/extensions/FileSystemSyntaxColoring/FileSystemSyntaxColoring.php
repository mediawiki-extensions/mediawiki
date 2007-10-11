<?php
/**
 * @author Jean-Lou Dupont
 * @package FileSystemSyntaxColoring
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'FileSystemSyntaxColoring', 
	'version'		=> '1.0.0',
	'author'		=> 'Jean-Lou Dupont', 
	'description'	=>  'Syntax highlights filesystem related pages',
	'url' 			=> 'http://mediawiki.org/wiki/Extension:FileSystemSyntaxColoring',			
);
StubManager::createStub(	'FileSystemSyntaxColoring', 
							dirname(__FILE__).'/FileSystemSyntaxColoring.body.php',
							null,
							array( 'ArticleAfterFetchContent', 'ParserBeforeStrip' ),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null,	// no magic words
							array( NS_FILESYSTEM )
						 );
//</source>
