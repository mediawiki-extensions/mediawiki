<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 * @version 1.4.1
 * @Id $Id: ImageLink.php 843 2008-01-05 22:43:01Z JeanLou.dupont $
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        	=> 'ImageLink', 
	'version'     	=> '1.4.1',
	'author'      	=> 'Jean-Lou Dupont', 
	'description' 	=> 'Provides a clickable image link',
	'url' 			=> 'http://mediawiki.org/wiki/Extension:ImageLink',			
);
if (class_exists('StubManager'))
	StubManager::createStub(	'ImageLink', 
								dirname(__FILE__).'/ImageLink.body.php',
								null,						// i18n file			
								null,						// hooks
								false, 						// no need for logging support
								null,						// tags
								array('imagelink', 'imagelink_raw', 'img' ),	// parser Functions
								null
							 );
else
	echo '[[Extension:ImageLink]] requires [[Extension:StubManager]].';							
//</source>