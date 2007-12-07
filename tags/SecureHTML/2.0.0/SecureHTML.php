<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureHTML
 * @version 2.0.0
 * @Id $Id: SecureHTML.body.php 660 2007-10-29 16:32:49Z jeanlou.dupont $
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'        => 'SecureHTML', 
	'version'     => '2.0.0',
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
							array( 'html' ),
							null,	// no magic words
							null	// no namespace triggering
						 );
//</source>