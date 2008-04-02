<?php
/**
 * @author Jean-Lou Dupont
 * @package PageServer
 * @category ExtensionServices
 * @version 2.0.0
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager') || version_compare( StubManager::version(), '1.2.0', '<' ) )
	echo "<a href='http://mediawiki.org/wiki/Extension:PageServer'/> <b>requires</b> <a href='http://mediawiki.org/wiki/Extension:StubManager'/>";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    	=> 'PageServer',
		'version' 	=> '2.0.0',
		'author'  	=> 'Jean-Lou Dupont',
		'description' => "Provides functionality to load & parse wiki pages stored in the filesystem.", 
		'url' 		=> 'http://mediawiki.org/wiki/Extension:PageServer',		
	);
		
	
	
	StubManager::createStub2(	array(	'class' 		=> 'PageServer', 
										'classfilename'	=> dirname(__FILE__).'/PageServer.body.php',
										'mgs'			=> array( 'mwmsg', 'mwmsgx', 'load_page' ),
										'hooks'			=> array( 'page_server' )										
									)
							);
}
//</source>