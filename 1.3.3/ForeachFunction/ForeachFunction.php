<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ForeachFunction
 * @version 1.3.3
 * @Id $Id: ForeachFunction.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'ForeachFunction', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Looping functions for global objects using parser functions',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:ForeachFunction',			
);

StubManager::createStub(	'ForeachFunction', 
							dirname(__FILE__).'/ForeachFunction.body.php',
							null,							
							null,
							false, // no need for logging support
							null,	// tags
							array( 'foreachx','foreachy','forx', 'foreachc' ),  //of parser function magic words,
							null
						 );
//</source>
