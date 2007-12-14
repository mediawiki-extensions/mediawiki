<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage UserTools
 * @version @@package-version@@
 * @Id $Id$
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

	/**
	 * {{#cusergetoption:which-option|default-if-not-found}}	
	 */
	public function mg_cusergetoption( &$parser, $whichOption, $default = null )
	{
		global $wgUser;
		
		// if the option is marked 'restricted', make sure
		// the current user has the right to access the requested 'option'
		if ($this->isRestricted( $option ))
			if (!$wgUser->isAllowed('userdetails'))	
				return 'UserTools: '.wfMsg('badaccess');

		return $this->getOption( $wgUser, $whichOption, $default );
	}
	/**
	 * {{#usergetoption:user-name-OR-id|which-option|default-if-not-found}}
	 */
	public function mg_usergetoption( &$parser, $user, $whichOption, $default = null )
	{
		global $wgUser;
		
		// if the option is marked 'restricted', make sure
		// the current user has the right to access the requested 'option'
		if ($this->isRestricted( $option ))
			if (!$wgUser->isAllowed('userdetails'))	
				return 'UserTools: '.wfMsg('badaccess');

		$userObj = $this->getUserObject( $user );
		
		return $this->getOption( $userObj, $whichOption, $default );
	}
	/**
	 * Returns 'true' (restricted) if the option is not found.
	 */
	private function isRestricted( &$option )
	{
		if (isset( self::$options[ $option ] ))
			$r = self::$options[ $option ];
		else
			return true;
			
		return ($r == self::RESTRICTED) ? true:false;
	}
	/**
	 * Returns the value of the specified option and $default if not found.
	 */
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
	/**
	 * {{#cuserfromgroup: groupname-to-look-for|value-to-return-when-found|value-to-return-when-not-found}}
	 *
	 * @param string $group Group name to look for
	 * @param mixed $trueValue Value to return in case current user is part of $group
	 * @param mixed $falseValue Value to return in case current user is not part of $group	 
	 */
	public function mg_cuserfromgroup(	&$parser, 
										$group, 
										$trueValue = true, 
										$falseValue = false )
	{
		global $wgUser;

		return $this->doGroupLookup( $wgUser, $group, $trueValue, $falseValue );
	}
	/**
	 * {{#userfromgroup: username-OR-user-id|groupname-to-look-for|value-to-return-when-found|value-to-return-when-not-found}}
	 *
	 * @param string $user Username OR user id
	 * @param string $group Group name to look for
	 * @param mixed $trueValue Value to return in case current user is part of $group
	 * @param mixed $falseValue Value to return in case current user is not part of $group	 
	 */
	public function mg_userfromgroup(	&$parser, 
										$user,
										$group, 
										$trueValue = true, 
										$falseValue = false )
	{
		// If the current user isn't allowed to viewing another user's group membership details.
		global $wgUser;
		if (!$wgUser->isAllowed('userdetails'))	
			return 'UserTools: '.wfMsg('badaccess');
		
		$userObj = $this->getUserObject( $user );

		return $this->doGroupLookup( $userObj, $group, $trueValue, $falseValue );
	}
	/**
	 *
	 */
	protected function doGroupLookup( &$user, &$group, $trueValue, $falseValue )
	{
		if ( !is_object( $user ))
			return $falseValue;
			
		$ugroups = $user->getEffectiveGroups();
		$result  = array_search( $group, $ugroups );
		
		return ( $result ) ? $trueValue:$falseValue;
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * @param mixed $user Either a string username or a user's ID
	 */
	protected function getUserObject( &$user )
	{
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

		return $userObj;		
	}	 

		
} // end class
//</source>