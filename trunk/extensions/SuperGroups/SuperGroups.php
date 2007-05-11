<?php
/*
 * SuperGroups.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:   
 *
 * 
 * Tested Compatibility: 
 *
 * DEPENDANCY: ExtensionClass
 * 
 * LocalSettings.php:
 * ==================
 * require("extensions/ExtensionClass.php");
 * require("extensions/SuperGroups/SuperGroups.php");
 *
 * HISTORY:	v1.0
 *
 */

// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'ExtensionClass missing: SuperGroups extension will not work!';	

require_once(dirname( __FILE__ ) . '/SuperGroupsClass.php');

SuperGroupsClass::singleton();

$wgExtensionCredits['SuperGroupsClass::$type'][] = array( 
	'name'    => SuperGroupsClass::$name,
	'version' => 'v1.0 $LastChangedRevision: 132 $',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

?>