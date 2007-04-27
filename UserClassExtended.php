<?php
/* 
 * UserClassExtended.php
 * MediaWiki extension
 * 
 * Provides a superclass for the Mediawiki "user class" 
 * (includes/user.php) supporting enhanced rights management
 * functionality. The rights management methods are provided
 * through 
 *
 * @author: Jean-Lou Dupont
 * www.bluecortex.com
 *
 * INSTALLATION:
 * =============
 * 1) Ideally, put in your "LocalSettings.php" file  
 *    the line "require_once("extensions/UserClassExtended.php");"
 *    as high as possible BUT after the "HierarchicalNamespacePermissions"
 *    extension.
 * 2) Configure the global object "hnpObj" as per the 
 *    "HierarchicalNamespacePermissions" extension documentation.
 * 
 * 3) Optional: apply the desired patches to other MW classes
 *    to reap the full benefits of this extension. 
 *    Examples:
 *    - SpecialRecentChanges.php : only list the changes which
 *      fall in allowable Namespace(s) for the current user.
 *    - SearchEngine.php : same as above
 *    - EditPage.php : implement a "viewsource" right
 *    etc.
 * 
 *    The statement :
 *     if ($wgUser->isAllowedEx($ns,$titlename,"viewsource")) 
 *        {code here}
 *     when adequately placed can serve to implement a "viewsource" right.
 * 
 * IMPLEMENTATION NOTES:
 * =====================
 * 1) This extension replaces the object global "wgUser" with
 *    an instance of "userClass" which acts as a superclass.
 *
 * FEATURES:
 * =========
 *  - No code change in the standard Mediawiki package.
 *
 *  - Integration with "HierarchicalNamespacePermissions" extension
 *    (which provides the rights management methods)
 *
 * MEDIAWIKI NOTES:
 * ================
 *
 * HISTORY:
 * ================
 * Version 1.0:   - Initial availability
 * Version 1.01:  - Minor addition to make it compatible with MW > 1.10
 */

$wgExtensionCredits['other'][] = array(
    'name'    => "UserClassExtended [http://www.bluecortex.com]",
	'version' => '$LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

// REQUIRED INCLUDES
require_once("includes/Exception.php");  // required for MW > 1.10
require_once("includes/StubObject.php");
require_once("includes/User.php");

// required for proper timing with MW class loading.
$wgExtensionFunctions[] = 'uceSetup';

/*  SUPERCLASS DEFINITION
   ***********************/
class UserClass extends User
{
	// For namespace independant rights.
	// This function overrides the parent User::isAllowed function.
	// ------------------------------------------------------------
	function isAllowed( $action='read'  )
	{
		// Sometimes "isAllowed" is called
		// with an empty $action variable.
		// This happens for example in "SpecialPage.php".
		if ($action=='')
			return true;
			
		return $this->isAllowedEx("~", "~" , $action);
	}
	// For namespace dependant rights.	
	function isAllowedEx( $ns, $pt, $action)
	{
		// Check if the extension
		// "Hierarchical Namespace Permission" is loaded
		// If not, continue normal processing.
		if ( !class_exists('hnpClass') )
			return true;

		// If the extension is loaded, reformat the "action query"
		// so that HNP understands it.
		return hnpClass::userCanInternal($this, $ns, $pt, $action);
	}
}

/*
 * Stub class used to intercept Mediawiki's processing flow
 * for the "wgUser" global object.
*/
class UserClassStub extends StubUser
{
	function __construct()	{ parent::__construct( 'wgUser' );	}

	function _newObject()
	{
		$user  = parent::_newObject();
		$user2 = new UserClass;

		// Clone the object in object instance
		// of our class i.e. substitution occurs here.	
		foreach ($user as $key => $value)
			$user2->{$key} = $value;	
		
		return $user2;	
	}
} # end class definition

/*************************************************
 * SETUP
*************************************************/
function uceSetup()
{
	// replace the standard global Mediawiki wgUser object
	// with one where we can do some object class substitution.
	global $wgUser;
	$wgUser = new UserClassStub;	
}
?>
