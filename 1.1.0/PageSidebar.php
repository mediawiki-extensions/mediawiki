<?php
/**
 * @author Jean-Lou Dupont
 * @package PageSidebar	
 * @version $Id: PageSidebar.php 1189 2008-06-18 00:39:55Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager'))
	echo '[[Extension:PageSidebar]] <b>requires</b> [[Extension:StubManager]]'."\n";
else
{
	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'PageSidebar',
		'version'		=> '1.1.0',
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