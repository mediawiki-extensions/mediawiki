<?php
/*
 * ScriptsManager.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * 
 * Purpose:   This Mediawiki extension enables a user with the 'commitscript' right
 * ========   to edit scripts in the NS_SCRIPTS namespace and commit those to filesystem.  
 *
 * Features:
 * =========
 * 1) New right: 'commitscript'
 * 2) New Namespace: 'NS_SCRIPTS'
 * 3) Logging
 *
 * DEPENDANCY:  
 * ===========
 * 1) Extension 'ExtensionClass' (>=v1.92) 
 *
 * USAGE NOTES:
 * ============
 * 1) An extra namespace ( NS_SCRIPTS ) must be declared in 'LocalSettings.php'.
 *
 * Tested Compatibility:  MW 1.8.2, 1.10
 * =====================
 *
 * History:
 * ========
 * - v1.0
 * - v1.01   - Added '__NOCOMMIT__' magic word
 *
 * TODO:
 * =====
 * -- Commit Action handling (currently save==commit)
 * 
 */

// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'ExtensionClass missing: ScriptsManager extension will not work!';	
else
{
	require( 'ScriptsManager.i18n.php' );
	require( "ScriptsManagerClass.php" );
	ScriptsManagerClass::singleton();
}
?>