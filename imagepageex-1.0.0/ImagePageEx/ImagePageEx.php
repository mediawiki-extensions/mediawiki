<?php
/**
 * @author Jean-Lou Dupont
 * @package ImagePageEx
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'		=> 'ImagePageEx',
	'version'	=> '$Id$',
	'author'	=> 'Jean-Lou Dupont',
	'url'		=> 'http://www.mediawiki.org/wiki/Extension:ImagePageEx',
	'description' => "Provides the hooks 'ImageDoDeleteBegin' & 'ImageDoDeleteEnd'.", 
);

global $IP;
global $wgExtensionFunctions;
require_once( $IP.'/includes/ImagePage.php' );
require( dirname(__FILE__).'/ImagePageEx.body.php' );	
$wgExtensionFunctions[] = create_function('', 'return ImagePageEx::setup();');

//</source>