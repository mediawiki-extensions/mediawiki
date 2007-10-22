<?php
/**
 * @author Jean-Lou Dupont
 * @package ViewsourceRight
 * @version $Id$
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'ViewsourceRight', 
	'version'		=> '1.1.0',
	'author'		=> 'Jean-Lou Dupont', 
	'description'	=> "Enforces 'viewsource' right. Status: ",
	'url'			=> 'http://mediawiki.org/wiki/Extension:ViewsourceRight',			
);

StubManager::createStub(	'ViewsourceRight', 
							dirname(__FILE__).'/ViewsourceRight.body.php',
							null,
							array( 'SpecialVersionExtensionTypes','AlternateEdit', 
									'SkinTemplateTabs', 'clearSkinTabActions'
								),
							false,	// no need for logging support
							null,	// tags
							null,	// no parser functions
							null	// no magic words
						 );
//</source>
