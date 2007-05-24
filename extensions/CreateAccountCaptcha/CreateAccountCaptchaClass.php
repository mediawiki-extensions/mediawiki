<?php
/*
 * CreateAccountCaptchaClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 */

class CreateAccountCaptchaClass extends ExtensionClass
{
	// constants.
	const thisName = 'CreateAccountCaptchaClass';
	const thisType = 'other';

/* ---------------------------------
   Initialization methods
   ---------------------------------
*/
	public static function &singleton()
	{ return parent::singleton( );	}
	
	function SmartyAdaptorClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( );

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.00 $Id$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Create Account Captcha',
		);
	}
	public function setup() 
	{ 
		parent::setup();
		
		// Messages.
		global $wgMessageCache, $wgSmartyAdaptorMessages;
		foreach( $wgSmartyAdaptorMessages as $key => $value )
			$wgMessageCache->addMessages( $wgSmartyAdaptorMessages[$key], $key );
	} 
	
	/* ---------------------------------
	   Hook handler method
	   ---------------------------------
	*/
	function hUserCreateForm( &$template )
	/*  This hook will call the processing script(s).
	 */
	{
		return true; // continue hook chain.
	}

	function hAbortNewAccount( $u, &$message )
	{
		
	}
	
/* ------------------------------------------------------------------
    SUPPORT METHODS                                                
   ------------------------------------------------------------------ */	
	
} // END CLASS DEFINITION
?>