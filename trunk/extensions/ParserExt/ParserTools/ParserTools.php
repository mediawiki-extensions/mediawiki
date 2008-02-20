<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ParserTools
 * @version @@package-version@@
 * @Id $Id$
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