<?php
/**
 * @author Jean-Lou Dupont
 * @package UserAccountManager
 * @version $Id$
 */
//<source lang=php>
class UserAccountManager
{
	const tPage = 'UserAccountManager.template.wikitext';
	static $tfile = null;
	static $cdir = null;
	const metaDataPage = '';

	// IMPORTANT: the order is important for the replace function!
	static $map = array(
		'mId'					=> 'id',
		'mRealName'				=> 'real_name',	
		'mName'					=> 'name',
		'mPassword'				=> 'password',
		'mEmailAuthenticated'	=> 'email_authenticated',
		'mEmail'				=> 'email',
		'mNewpassTime'			=> 'new_password_time',		
		'mNewpassword'			=> 'new_password',
		'mTouched'				=> 'touched',
		'mEmailTokenExpires'	=> 'token_email_expires',
		'mEmailToken'			=> 'token_email',
		'mToken'				=> 'token',
		'mRegistration'			=> 'registration',
	);
	
	public function __construct() 
	{
		self::$cdir  = dirname(__FILE__);
		self::$tfile = self::$cdir.'/'.self::tPage;
	}
	
	/**
	 * New user account creation hook
	 */
	public function hAddNewAccount( &$user )
	{
		$this->doUpdate( $user );		
		return true;
	}
	/**
	 * Specific hook from [[Extension:BizzWiki]]
	 */
	public function hUserSettingsChanged( &$user )
	{
		// case 1: new account creation
		// Just bail out.
		global $wgUser;
		if ( $wgUser->getID() == 0 )
			return true;

		// Case 2:
		// we need some protection against multiple saves per transaction.
		// SpecialPreferences.php does multiple saves regularly...
		static $firstTimePassed = false;
		
		if ($firstTimePassed === false)
		{
			$firstTimePassed = true;
			return true;
		}
		
		$this->doUpdate( $user, false );
		return true;
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	/**
	 */
	protected function doUpdate( &$user, $new = true )
	{
		$template = $this->loadTemplate();
		
		$this->fillTemplate( $user, $template );
		
		$this->saveUserMetaData( $user, $template, $new );
	}
	/**
	 */
	protected function loadTemplate()
	{
		return @file_get_contents( self::$tfile );	
	}	 
	/**
	 */
	protected function fillTemplate( &$user, &$template )	
	{
		foreach( User::$mCacheVars as $var )
		{
			if (isset( self::$map[ $var ]))
				$map = self::$map[ $var ];	
			else
				continue;
			
			// get the user's value for a given parameter
			$value = $user->$var;
			
			// fill in the template
			$template = str_replace( '$'.$map.'$', $value, $template );
		}
		
		$this->fillOptions( $user, $template );
	}
	/**
	 */
	protected function fillOptions( &$user, &$template )
	{
		$options = $user->encodeOptions();
		$o = explode("\n", $options);
		if (empty( $o ))
			return;
			
		$liste = '';
		
		// prepare the tag section
		foreach( $o as $option )
		{
			$parts = explode('=', $option );
			$key = $parts[0];
			if (isset( $parts[1]))
				$value = ' value="'.$parts[1].'"';
			else 
				$value = null;
			$liste .= "\t\t".'<'."$key".$value."><".'/'."$key>\n";
		}
		// insert it in the template
		$template = str_replace('$options$', $liste, $template );
	}
	/**
	 * Save the metadata on page: [[User:$name.metadata]]
	 */		
	protected function saveUserMetaData( &$user, &$data, $new = true )
	{
		$pageTitle = $user->getName().'.metadata';
		$title = Title::newFromText( $pageTitle, NS_USER );
		
		$a = new Article( $title );
		if ( is_null($a) )
		{
			// this shouldn't happen anyways.
			throw new MWException( __METHOD__ );
		}
		else
		{
			if ($new)
				$flags = EDIT_NEW | EDIT_DEFER_UPDATES;
			else
				$flags = EDIT_UPDATE | EDIT_DEFER_UPDATES;
			$a->doEdit( $data, ' ', $flags );			
		}
		// DEBUG only.
		//$result = file_put_contents( self::$cdir.'/'.'UserAccountManager.export.xml', $data );	
	}
} // end class
//</source>