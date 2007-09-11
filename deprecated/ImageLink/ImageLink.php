<?php
/*
 * ImageLink.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 * Purpose:  Provides a clickable image link using
 *           an image stored in the Image namespace and
 *           an article title (which may or may not exist
 *           in the database). 
 *
 * USAGE E.g.:	{{#imagelink:New Clock.gif|Admin:Show Time|alternate text | width | height | border }}
 * 
 * Tested Compatibility: MW 1.8.2, 1.9.3, 1.10
 *
 * DEPENDANCY: ExtensionClass
 * 
 * LocalSettings.php:
 * ==================
 * require("extensions/ExtensionClass.php");
 * require("extensions/ImageLink.php");
 *
 * HISTORY:	v1.0
 *          v1.1  -- re-crafted to derive from 'ExtensionClass'
 *          v1.2  -- adding proofing against 'bad titles'
 *          v1.3  -- small fix regarding hook chaining
 *          v1.4  -- changed hook method for better parser caching integration.
 * -------------  -- changed to SVN management
 *          v1.5  -- Support for inter-wiki links (thanks to Andrew Culver)
 *          v1.51 -- Added check for ExtensionClass dependancy.
 *          v1.52 -- Fixed hook chain bug.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'ImageLink',
	'version' => 'v1.52 $Id$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'ExtensionClass missing: ImageLink extension will not work!';	
else
	require(dirname( __FILE__ ) . '/ImageLinkClass.php');

ImageLinkClass::singleton();

?>