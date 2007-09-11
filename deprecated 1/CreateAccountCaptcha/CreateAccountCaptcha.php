<?php
/*
 * CreateAccountCaptcha.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 * Purpose:  This extension provides a very lightweight but effective captcha
 * ========  used for regulating access to account creation.     
 *
 * Features:
 * =========
   1) No patches to Mediawiki installation
   2) Automatically generated random images containing text strings 

 * DEPENDANCIES:  
 * =============
   1) Extension 'ExtensionClass' (>=v1.92)
 
 * INSTALLATION NOTES:
 * ===================
 
 * LocalSettings.php:
 * ==================
   
   require("extensions/ExtensionClass.php");
   require("extensions/CreateAccountCaptcha/CreateAccountCaptcha.php");
 
 * NOTES:
 * ======
   In Userlogin.php:
	<p id="userloginlink"><php $this->html('link') ></p>
	<php $this->html('header');
 
 * Tested Compatibility:  1.8.2
 * =====================

 * History:
 * ========
   - v1.0
  
 
 * TODO:
 * =====

 */
 
// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'CreateAccountCaptcha extension: ExtensionClass missing.';	
else
{
	$wgAutoloadClasses['CreateAccountCaptchaClass'] = 'extensions/CreateAccountCaptcha/CreateAccountCaptchaClass.php';
	
	$wgHooks['UserCreateForm'][] = 
		create_function( '&$template', 
						'return CreateAccountCaptchaClass::singleton()->hUserCreateForm(&$template);');
	
	$wgHooks['AbortNewAccount'][] = 
		create_function( '$user, &$message', 
						'return CreateAccountCaptchaClass::singleton()->hAbortNewAccount($user,&$message);');
}
?>