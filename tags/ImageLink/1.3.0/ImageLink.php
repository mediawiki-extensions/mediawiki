<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 * @version 1.3.0
 * @Id $Id: ImageLink.php 763 2007-12-14 20:23:58Z jeanlou.dupont $
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        	=> 'ImageLink', 
	'version'     	=> '1.3.0',
	'author'      	=> 'Jean-Lou Dupont', 
	'description' 	=> 'Provides a clickable image link',
	'url' 			=> 'http://mediawiki.org/wiki/Extension:ImageLink',			
);
if (class_exists('StubManager'))
	StubManager::createStub(	'ImageLink', 
								dirname(__FILE__).'/ImageLink.body.php',
								null,						// i18n file			
								array('ParserAfterTidy'),	// hooks
								false, 						// no need for logging support
								null,						// tags
								array('imagelink', 'imagelink_raw', 'img' ),	// parser Functions
								null
							 );
else
	echo '[[Extension:ImageLink]] requires [[Extension:StubManager]].';							
//</source>