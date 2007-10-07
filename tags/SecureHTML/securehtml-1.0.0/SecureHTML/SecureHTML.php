<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureHTML
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'        => 'SecureHTML', 
	'version'     => StubManager::getRevisionId( '$Id$' ),
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Enables secure HTML code on protected pages',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:SecureHTML',			
);

StubManager::createStub(	'SecureHTML', 
							dirname(__FILE__).'/SecureHTML.body.php',
							null,
							array( 'ArticleSave', 'ArticleViewHeader' ),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null,	// no magic words
							null	// no namespace triggering
						 );
//</source>