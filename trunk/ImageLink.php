<?php
/*
 * ImageLink.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a clickable image link using
 *           an image stored in the Image namespace and
 *           an article title (which may or may not exist
 *           in the database). 
 *
 * USAGE E.g.:	{{#imagelink:New Clock.gif|Admin:Show Time|alternate text | width | height | border }}
 * 
 * Tested Compatibility: MW 1.8.2, 1.9.3
 *
 * DEPENDANCY: ExtensionClass
 * 
 * HISTORY:	v1.0
 *          v1.1 -- re-crafted to derive from 'ExtensionClass'
 *          v1.2 -- adding proofing against 'bad titles'
 *          v1.3 -- small fix regarding hook chaining
 *          v1.4 -- changed hook method for better parser caching integration.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'ImageLink',
	'version' => '$LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once(dirname( __FILE__ ) . '/ImageLinkClass.php');

ImageLinkClass::singleton();

?>