<?php
/**
 * @author Jean-Lou Dupont
 * @package ViewsourceRight
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'ViewsourceRight', 
	'version'		=> StubManager::getRevisionId( '$Id$' ),
	'author'		=> 'Jean-Lou Dupont', 
	'description'	=> "Enforces 'viewsource' right. Status: ",
	'url'			=> 'http://mediawiki.org/wiki/Extension:ViewsourceRight',			
);

StubManager::createStub(	'ViewsourceRight', 
							dirname(__FILE__).'/ViewsourceRight.body.php',
							null,
							array( 'SpecialVersionExtensionTypes','AlternateEdit', 'SkinTemplateTabs' ),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null	// no magic words
						 );
//</source>
