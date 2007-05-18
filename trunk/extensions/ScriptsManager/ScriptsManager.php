<?php
/*
 * ScriptsManager.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * 
 * Purpose:  
 * ========  
 *
 * Features:
 * *********
 *
 * DEPENDANCY:  ExtensionClass (>=v1.92)
 * ===========
 * 
 * USAGE NOTES:
 * ============
 * 1) An extra namespace ( NS_SCRIPTS ) must be declared
 *    in 'LocalSettings.php'.
 *
 * Tested Compatibility:  MW 1.8.2, 1.10
 * =====================
 *
 * History:
 * ========
 * - v1.0
 *
 * TODO:
 * =====
 * -- Commit Action
 * 
 */

// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'ExtensionClass missing: ScriptsManager extension will not work!';	
else
{
	require("ScriptsManagerClass.php");
	ScriptsManagerClass::singleton();
}
?>