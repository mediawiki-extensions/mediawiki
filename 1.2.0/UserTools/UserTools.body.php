<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage UserTools
 * @version 1.2.0
 * @Id $Id: UserTools.body.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
class UserTools
{
	const thisName = 'UserTools';
	const thisType = 'other';

	const RESTRICTED   = 0;
	const UNRESTRICTED = 1;

	static $options = array(
								// require special treatment
								'email'			=> self::RESTRICTED,
								'realname'		=> self::RESTRICTED,
								'authtimestamp'	=> self::RESTRICTED,
								'datepref'		=> self::UNRESTRICTED,

								// can be retrieved through 'User::getOption'
								'language'		=> self::UNRESTRICTED,
								'variant'		=> self::UNRESTRICTED,
								'disablemail'	=> self::UNRESTRICTED,
								'nickname'		=> self::UNRESTRICTED,
								'quickbar'		=> self::UNRESTRICTED,
								'skin'			=> self::UNRESTRICTED,																
								'math'			=> self::UNRESTRICTED,
								'rows'			=> self::UNRESTRICTED,
								'cols'			=> self::UNRESTRICTED,								
								'stubthreshold'	=> self::UNRESTRICTED,																
								'timecorrection'=> self::UNRESTRICTED,
								'searchlimit'	=> self::UNRESTRICTED,
								'contextlines'	=> self::UNRESTRICTED,
								'contextchars'	=> self::UNRESTRICTED,																								
								'imagesize'		=> self::UNRESTRICTED,																								
								'thumbsize'		=> self::UNRESTRICTED,
								'rclimit'		=> self::UNRESTRICTED,
								'rcdays'		=> self::UNRESTRICTED,
								'wllimit'		=> self::UNRESTRICTED,
								'underline'		=> self::UNRESTRICTED,
								'watchlistdays'	=> self::UNRESTRICTED,
							);

	public function __construct() {}
	
	/**
	
	 */
	public function mg_cusergetoption( &$parser, $whichOption, $default = null )
	{
		global $wgUser;
		
		// if the option is marked 'restricted', make sure
		// the current user has the right to access the requested 'option'
		if ($this->isRestricted( $option ))
			if (!$wgUser->isAllowed('userdetails'))	
				return null;

		return $this->getOption( $wgUser, $whichOption, $default );
	}
	/**
	 */
	public function mg_usergetoption( &$parser, $user, $whichOption, $default = null )
	{
		global $wgUser;
		
		// if the option is marked 'restricted', make sure
		// the current user has the right to access the requested 'option'
		if ($this->isRestricted( $option ))
			if (!$wgUser->isAllowed('userdetails'))	
				return null;

		if (is_numeric( $user ))
		{
			$userObj = User::newFromId( $user );
			if (!is_object( $userObj ))
				return null;
			if ($userObj->getID() == 0)
				$userObj = User::newFromName( $user, true /* validate */);
		}
		else
			$userObj = User::newFromName( $user, true /* validate */);

		if (!is_object( $userObj ))
			return null;
		
		return $this->getOption( $userObj, $whichOption, $default );
	}

	/**
		Returns 'true' (restricted) if the option is not found.
	 */
	private function isRestricted( &$option )
	{
		if (isset( self::$options[ $option ] ))
			$r = self::$options[ $option ];
		else
			return true;
			
		return ($r == self::RESTRICTED) ? true:false;
	}
	public function getOption( &$user, &$option, $default = null )
	{
		switch( $option )
		{
			case 'email':
				return $user->getEmail();
			case 'realname':
				return $user->getRealName();
			case 'authtimestamp':
				return $user->getEmailAuthenticationtimestamp();
			case 'datepref':
				return $user->getDatePreference();
			default:
				return $user->getOption( $option, $default );
		}

		return null; // calms PHP			
	}
	
} // end class
//</source>