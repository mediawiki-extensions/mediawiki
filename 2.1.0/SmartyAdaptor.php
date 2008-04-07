<?php
/**
 * @author Jean-Lou Dupont
 * @package SmartyAdaptor
 * @version 2.1.0
 * @Id $Id: SmartyAdaptor.php 989 2008-04-07 13:35:38Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager') || version_compare( StubManager::version(), '1.2.0', '<' ) )
	echo "<a href='http://mediawiki.org/wiki/Extension:SmartyAdaptor'/> <b>requires</b> <a href='http://mediawiki.org/wiki/Extension:StubManager'/>";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'SmartyAdaptor',
		'version'		=> '2.1.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:SmartyAdaptor',	
		'description' 	=> "Provides interface to Smarty [http://smarty.net Smarty Template Engine]. ", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'SmartyAdaptor', 
										'classfilename'	=> dirname(__FILE__).'/SmartyAdaptor.body.php',
										'hooks'			=> array(	'smarty', 
																	'smarty_direct',
																	'SpecialVersionExtensionTypes' )
									)
							);
}
//</source>