<?php
/*
 * SmartyAdaptor.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
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
 * DEPENDANCIES:  
 * =============
   1) Extension 'ExtensionClass' (>=v1.92)
   2) Smarty framework (available @ ) 
 
 * FILESYSTEM LAYOUT:
 * ==================
       $IP/extensions/SmartyAdaptor   [extension files]
		  /scripts/Smarty             [framework files]
		  /scripts/Smarty/processors  [processor files]
		  /scripts/Smarty/templates   [template  files]
 
   (note that the variable $IP is defined in Mediawiki)
 
 * USAGE NOTES:
 * ============
   {{#smarty: 'processor' | 'template' }}
 
   1) Do not append the '.php' to the processor script filename
   2) Do not append the '.tpl' to the template script filename
 
   3) Smarty processor scripts must be located in: $base/$procs
   4) Smarty templates must be located in: $base/$tpl
   5) Smarty framework must be located located in $base/$smarty
   6) Smarty processor scripts must be contain a class definition
      matching the filename (minus the file's extension).
	  E.g.  SmartyCommentForm.php  must contain a class definition
	        named 'SmartyCommentForm'.
   7) Processor Scripts classes must be extended from the 'Smarty' class
   
   8) Smarty processor scripts are assumed to have the .php file extension
   9) Smarty template scripts are assumed to have the .tpl file extension   
 
 
 * LocalSettings.php:
 * ==================
   require("extensions/ExtensionClass.php");
   require("extensions/SmartyAdaptor.php");
 
 * Tested Compatibility:  1.8.2
 * =====================
 *
 * History:
 * ========
   - v1.0
  
 
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