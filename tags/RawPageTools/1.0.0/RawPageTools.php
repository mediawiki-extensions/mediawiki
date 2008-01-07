<?php
/**
 * @author Jean-Lou Dupont
 * @package RawPageTools
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'    => 'RawPageTools',
		'version' => '1.0.0',
		'author'  => 'Jean-Lou Dupont',
		'description' => 'Provides removal of `js` and `css` tag sections for raw page functionality', 
	);
	
	StubManager::createStub(	'RawPageTools', 
								dirname(__FILE__).'/RawPageTools.body.php',
								null,							
								array( 'RawPageViewBeforeOutput' ),
								false
							 );
}
else
	echo '[[Extension:RawPageTools]] requires [[Extension:StubManager]]';

//</source>
