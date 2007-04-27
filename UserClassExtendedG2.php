<?php
/* 
 * UserClassExtendedG2.php
 * MediaWiki extension
 * 
 * Provides a superclass for the Mediawiki "user class" 
 * (includes/user.php) supporting enhanced rights management
 * functionality.
 *
 * This extension builds on 'UserClassExtended'.
 *
 * @author: Jean-Lou Dupont
 * www.bluecortex.com
 *
 * IMPLEMENTATION NOTES:
 * =====================
 * 1) This extension replaces the object global "wgUser" with
 *    an instance of "userClassG2" which acts as a superclass.
 *
 * 2) There is a maximum of 16 characters for defining a group
 *
 * FEATURES:
 * =========
 *  - No code change in the standard Mediawiki package.
 *
 *  - Integration with "HierarchicalNamespacePermissions" extension
 *    (which provides the rights management methods)
 *
 *  - Provides a get/set interface for specifying a 'Service Instance'
 *    property for each user. This property is stored in the 'group'
 *    field prefixed by 'si_'. 
 *
 *    This functionality enables the support of 'partitioning' a
 *    Mediawiki installation amongst several segregated 'services'.
 *   
 *    The full benefits of this functionality is only achieved through
 *    the orchestration with other companion extensions. 
 *
 * HISTORY:
 * ================
 *
 */

$wgExtensionCredits['other'][] = array(
    'name'    => "UserClassExtendedG2 [http://www.bluecortex.com]",
	'version' => '$LastChangedRevision: 39 $',
	'author'  => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

// REQUIRED INCLUDES
require_once("includes/Exception.php");  // required for MW > 1.10
require_once("includes/User.php");

// Required for proper timing with MW class loading.
// Note that we are pushing the initialisation of this class at the top of the stack. 
// Also note that we are not implementing 'stubbing'.
array_unshift( 	$wgExtensionFunctions, 
				create_function('','$GLOBALS[`wgUser`]= UserClassG2::loadFromSession();') 
			);

/*  SUPERCLASS DEFINITION
   ***********************/
class UserClassG2 extends User
{
	public function UserClassG2()
	{ return parent::__construct();	}

###################################################################################
/*
    New Methods
*/
###################################################################################
	public function getSI() 
	{ 
		$g = $this->getGroups();
		
		$r = null; // assume the worst.
		
		// scan through the list of groups
		// for an instance beginning with 'si_'
		foreach( $g as $el )
			if ( substr($el, 0, 3) === 'si_' )
				$r = substr( $el, 3 );
			
		return $r; 
	}
	
	public function setSI( $si )
	{ parent::addGroup( 'si_'.$si ); }
	
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
} # end class definition
?>