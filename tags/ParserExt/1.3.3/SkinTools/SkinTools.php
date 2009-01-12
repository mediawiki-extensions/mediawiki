<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage SkinTools
 * @version 1.3.3
 * @Id $Id: SkinTools.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'SkinTools', 
	'version'     => '1.1.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Provides skin level functions',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:SkinTools',						
);

StubManager::createStub2(	array(	'class' 		=> 'SkinTools', 
									'classfilename'	=> dirname(__FILE__).'/SkinTools.body.php',
									'hooks'			=> array( 'SkinTemplateTabs' ),
									'mgs'			=> array( 'clearactions', 'removeactions', 'addaction' ),
								)
						);
//</source>