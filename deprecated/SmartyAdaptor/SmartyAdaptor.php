<?php
/*
 * SmartyAdaptor.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 * Purpose:  This extension provides access to the Smarty PHP template
 * ========  processor framework. See http://smarty.php.net/ for more details.     
 *
 * Features:
 * =========
   1) Integration with Mediawiki's Parser Cache functionality
      - An article can contain cached wikitext as well as Smarty template calls.
 
   2) Usage through a parser 'magic word'
   3) Smarty Config files support
 
 
 * DEPENDANCIES:  
 * =============
   1) Extension 'ExtensionClass' (>=v1.92)
   2) Smarty framework (available @ http://smarty.php.net/download.php ) 
 
 * FILESYSTEM LAYOUT:
 * ==================
       $IP/extensions/SmartyAdaptor   [extension files]
		  /scripts/Smarty             [framework files]
		  /scripts/Smarty/processors  [processor files]
		  /scripts/Smarty/templates   [template  files]
		  
     * directories required by Smarty
	 --------------------------------
	 	  
		  /scripts/Smarty/templates/compile   [compile directory]		  
		  /scripts/Smarty/templates/cache     [template cache directory]		  
 
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
 
 * INSTALLATION NOTES:
 * ===================
   1) Download the Smarty framework from the link provided in 'Dependancies' above.
   2) Put the Smarty framework files according to the filesystem layer (see above).
   3) Create the 'compile' and 'cache' directories
   4) Adjust the permissions for all directories (Linux/Unix)
   5) Adjust 'LocalSettings.php' according to the instructions listed below.
 
 * LocalSettings.php:
 * ==================
   
   $wgFullInstallDir = dirname( __FILE__ );   // required when using a Windows based system
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
 
 */
 
if (!isset($wgFullInstallDir))
	echo 'Smarty Adaptor Extension: variable $wgFullInstallDir not defined in LocalSettings.php';
	
// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'Smarty Adaptor extension: ExtensionClass missing.';	
else
{
	require( 'SmartyAdaptor.i18n.php' );
	require( "SmartyAdaptorClass.php" );
	SmartyAdaptorClass::singleton();
}
