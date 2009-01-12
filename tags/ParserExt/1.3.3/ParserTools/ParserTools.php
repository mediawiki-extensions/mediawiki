<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ParserTools
 * @version 1.3.3
 * @Id $Id: ParserTools.php 934 2008-02-20 16:22:20Z jeanlou.dupont $
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'ParserTools', 
	'version'     => '1.1.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Parser cache enabling/disabling through <noparsercaching/> tag',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:ParserTools',			
);
StubManager::createStub2(	array(	'class' 		=> 'ParserTools', 
									'classfilename'	=> dirname(__FILE__).'/ParserTools.body.php',
									'mgs'			=> array( 'parsercacheexpire' ),
									'tags'			=> array( 'noparsercaching' ),
								)
						);
//</source>