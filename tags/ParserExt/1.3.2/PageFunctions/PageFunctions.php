<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage PageFunctions
 * @version 1.3.2
 * @Id $Id: PageFunctions.php 778 2007-12-19 15:18:35Z jeanlou.dupont $
 */
//<source lang=php>*/
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'        => 'PageFunctions', 
		'version'     => '1.2.0',
		'author'      => 'Jean-Lou Dupont', 
		'description' => 'Provides page scope functions',
		'url' 		=> 'http://mediawiki.org/wiki/Extension:PageFunctions',						
	);
	StubManager::createStub(	'PageFunctions', 
								dirname(__FILE__).'/PageFunctions.body.php',
								null,
								array( 'PageVarGet', 'PageVarSet', 
										'BeforePageDisplay' #for page title clear functionality
										),
								false, // no need for logging support
								null,	// tags
								array( 'pagetitle','pagesubtitle','pageexists',
										'varset', 'varget',
										'varaset', 'varaget',
										'varcapset',
										'cshow',
										'noext',
										'imgviewurl'		// new in 1.2.0
										 ),  				//of parser function magic words,
								array( 'noclientcaching' )	// magic words
							 );
}
else 
	echo "Extension:PageFunctions requires Extension:StubManager.";						 
//</source>
