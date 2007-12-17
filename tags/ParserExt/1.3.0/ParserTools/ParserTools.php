<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ParserTools
 * @version 1.3.0
 * @Id $Id: ParserTools.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>*/
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'ParserTools', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Parser cache enabling/disabling through <noparsercaching/> tag',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:ParserTools',			
);
StubManager::createStub(	'ParserTools', 
							dirname(__FILE__).'/ParserTools.body.php',
							null,							
							null,
							false, 						// no need for logging support
							array('noparsercaching'),	// tags
							null,
							null
						 );
//</source>
