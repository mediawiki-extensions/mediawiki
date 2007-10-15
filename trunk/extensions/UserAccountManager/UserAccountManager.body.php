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
	
	const metaDataPage = '';
	
	static $map = array(
		'mId'					=> 'id',
		'mName'					=> 'name',
		'mRealName'				=> 'real_name',
		'mPassword'				=> 'password',
		'mEmail'				=> 'email',
		'mEmailAuthenticated'	=> 'email_authenticated',
		'mNewpassword'			=> 'new_password',
		'mNewpassTime'			=> 'new_password_time',
		'mTouched'				=> 'touched',
		'mToken'				=> 'token',
		'mEmailToken'			=> 'token_email',
		'mEmailTokenExpires'	=> 'token_email_expires',
		'mRegistration'			=> 'registration',
		'mEditCount'			=> 'edit_count',
	);
	
	public function __construct() 
	{
		self::$cdir  = dirname(__FILE__);
		self::$tfile = self::$cdir.'/'.self::tPage;
	}
	
	/**
	 */
	public function hAddNewAccount( &$user )
	{
		$template = $this->loadTemplate();
		
		$this->fillTemplate( $user, $template );
		
		$this->saveUserMetaData( $user, $template );
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
		foreach( $user->mCacheVars as $var )
		{
			if (isset( self::$map[ $var ]))
				$map = self::$map[ $var ];	
			else
				continue;
			
			// get the user's value for a given parameter
			$value = $user->$var;
			
			// fill in the template
			$template = str_replace( '$'.$map, $value, $template );
		}
		
		$this->fillOptions( &$user, &$template );
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
		foreach( $o as $key => &$value )
			$liste .= '<'."$key".">$value<".'/'."$key>";	
		// insert it in the template
		$template = str_replace('$options', $liste, $template );
	}
	/**
	 */		
	protected function saveUserMetaData( &$user, &$data )
	{
		$result = file_put_contents( self::$cdir.'/'.'UserAccountManager.export.xml', $data );	
	}
} // end class
//</source>