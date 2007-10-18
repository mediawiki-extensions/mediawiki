<?php
/**
 * @author Jean-Lou Dupont
 * @package PageMetaData
 * @version $Id$
*/
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        => 'PageMetaData', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Provides saving page metadata (e.g. restrictions) to a wikitext page",
	'url'		=> 'http://mediawiki.org/wiki/Extension:PageMetaData',
);
StubManager::createStub2(	array(	'class' 		=> 'PageMetaData', 
									'classfilename'	=> dirname(__FILE__).'/PageMetaData.body.php',
									'hooks'			=> array(	'ArticleProtectComplete' ),
								)
						);
//</source>