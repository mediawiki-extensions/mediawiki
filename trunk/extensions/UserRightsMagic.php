<?php
/*
 * UserRightsMagic.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides 'user rights' parameters through 'magic' words.
 *          
 *
 * Features:
 * *********
 * -- {{#usercan:action}} returns 'true' if the current user
 *                        can perform 'action'.
 *    e.g. {{#usercan:edit}}
 *
 * -- {{#userin:group}} returns 'true' if the current user
 *                      is a member of 'group'
 *    e.g. {{#userin:sysop}}
 *    
 * -- {{#usergroup:index}} returns the group name at 'index'
 *                         in the current user's group membership list.
 *    e.g. {{#usergroup:0}}
 *
 * -- {{#usergroupcount:}} returns the number of groups the current user
 *                         is member of *not* counting the '*' (default group)
 *                         and 'user' (default for logged in users).
 *    e.g. {{#usergroupcount:}}
 *
 * -- {{#userlogged:}}     returns 'true' if the current user is logged.
 *    e.g. {{#userlogged:}}
 *
 * DEPENDANCIES:
 * 1) 'ExtensionClass' extension
 *
 * Tested Compatibility:  MW 1.8.2, 1.9.3
 *
 * HISTORY:
 * -- Version 1.0:	
 *          
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'UserRightsMagic Extension', 
	'version' => '$LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

// Let's create a single instance of this class
// and give it the global name 'urObj'
UserRightsMagicClass::singleton();

class UserRightsMagicClass extends ExtensionClass
{
	static $mgwords = array('usercan', 'userin','usergroup', 'usergroupcount','userlogged');
	
	public static function &singleton( )
	{ return parent::singleton( ); }
	
	// Our class defines magic words: tell it to our helper class.
	public function UserRightsMagicClass()
	{	return parent::__construct( self::$mgwords );	}

	// ===============================================================
	public function mg_usercan( &$parser, $right )
	{
		$t = $parser->mTitle;
		return $t->userCan( $right );
	}
	public function mg_userin( &$parser, $group )
	{
		global $wgUser;
		$groups = $wgUser->getEffectiveGroups();
		return in_array( $group, $groups );
	}
	public function mg_usergroup( &$parser, $index )
	{
		global $wgUser;
		$groups = $wgUser->getGroups();
		if ($index<count($groups)) 
			return $groups[$index];		
	}
	public function mg_usergroupcount( &$parser )
	{
		global $wgUser;
		$groups = $wgUser->getGroups();
		return count($groups);
	}
	public function mg_userlogged( &$parser )
	{
		global $wgUser;
		return $wgUser->isLoggedIn();	
	}
} // end class	
?>