<?php
/*
 * SmartyAdaptor.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$ 
 *
 * Purpose:   
 * ========     
 *
 * Features:
 * =========
 *
 *
 *
 *
 * DEPENDANCY:  
 * ===========
 * 1) Extension 'ExtensionClass' (>=v1.92) 
 *
 * USAGE NOTES:
 * ============
 *
 *
 * Tested Compatibility:  
 * =====================
 *
 * History:
 * ========
 * - v1.0
 * 
 *
 * TODO:
 * =====
 * 
 * 
 */

// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'ExtensionClass missing: SmartyAdaptor extension will not work!';	
else
{
	require( 'SmartyAdaptor.i18n.php' );
	require( "SmartyAdaptorClass.php" );
	SmartyAdaptorClass::singleton();
}
?>