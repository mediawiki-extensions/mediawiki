<?php
/**
 * @author Jean-Lou Dupont
 * @package AutoLanguage
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'AutoLanguage', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Automatic page language switching based on user preference',
	'url'		=> 'http://mediawiki.org/wiki/Extension:AutoLanguage',
);

StubManager::createStub(	'AutoLanguage', 
							dirname(__FILE__).'/AutoLanguage.body.php',
							null,							
							array('ArticleFromTitle'),
							false
						 );
//</source>
