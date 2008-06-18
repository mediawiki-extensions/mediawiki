<?php
/**
 * @author Jean-Lou Dupont
 * @package PageSidebar	
 * @version $Id$
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:PageSidebar]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'PageSidebar',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:PageSidebar',	
		'description' 	=> "Provides per-page arbitrary wikitext for the sidebar.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'PageSidebar', 
										'classfilename'	=> dirname(__FILE__).'/PageSidebar.body.php',
										'hooks'			=> array( 'SkinTemplateOutputPageBeforeExec', 'OutputPageParserOutput','PageSidebar' ),
										'tags'			=> array( 'pagesidebar' ),
									)
							);
}
// </source>