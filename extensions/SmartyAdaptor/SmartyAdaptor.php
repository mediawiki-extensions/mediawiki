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
 * 2) Smarty framework (available @ ) 
 *
 * USAGE NOTES:
 * ============
 * {{#smarty: 'processor' | 'template' }}
 *
 * 1) Smarty processor scripts must be located in: $base/$procs
 * 2) Smarty templates must be located in: $base/$tpl
 * 3) Smarty framework must be located located in $base/$smarty
 *
 * Tested Compatibility:  1.8.2
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