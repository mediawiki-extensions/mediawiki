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
	
	const captchaScript = '/extensions/CreateAccountCaptcha/qc-imagebuilder.php';

/* ---------------------------------
   Initialization methods
   ---------------------------------
*/
	public static function &singleton()
	{ return parent::singleton( );	}
	
	function CreateAccountCaptchaClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( null, self::mw_style, 1, false );

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
		session_start();
		
		$imageSrc = self::captchaScript.'?sname='.session_name();
		
		$markup = <<<EOT
<div class='captcha'>
	<table>
		<tr>
	 		<td align='right'>Captcha :</td>
	 		<td align='left'><img src='{$imageSrc}' border='1' alt='captcha' /></td>
		</tr>
	</table>
</div>
EOT;
	
		$template->set( 'header', $markup);
		
		return true; // continue hook chain.
	}

	function hAbortNewAccount( $u, &$message )
	{
		if ($this->verifyCaptcha( &$result ))
			$message = wfMsg( 'createaccountcaptcha-create-success' );
		else
			$message = wfMsg( 'createaccountcaptcha-create-fail' );
			
		return $result;	
	}
	
/* ------------------------------------------------------------------
    SUPPORT METHODS                                                
   ------------------------------------------------------------------ */	
	private function verifyCaptcha( &$result )
	{
		$result = true;
		
		return $result; 	
	}	
} // END CLASS DEFINITION
?>