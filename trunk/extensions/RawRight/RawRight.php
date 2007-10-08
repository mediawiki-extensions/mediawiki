<?php
/**
 * @author Jean-Lou Dupont
 * @package RawRight
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'RawRight', 
	'version'		=> StubManager::getRevisionId( '$Id$' ),
	'author'		=> 'Jean-Lou Dupont', 
	'description'	=> "Status: ",
	'url'			=> 'http://mediawiki.org/wiki/Extension:RawRight',			
);

StubManager::createStub(	'RawRight', 
							dirname(__FILE__).'/RawRight.body.php',
							null,
							array( 'SpecialVersionExtensionTypes','RawPageViewBeforeOutput' ),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null	// no magic words
						 );
//</source>
