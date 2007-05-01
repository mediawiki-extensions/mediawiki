<?php
/* 
 * UserClassExtendedG2.php   
 * MediaWiki extension
 * 
 * @author: Jean-Lou Dupont
 * www.bluecortex.com
 *
 * IMPLEMENTATION NOTES:
 * =====================
 * 1) Need a patched version of 'User.php'
 *
 * DEPENDANCIES:
 * =============
 *
 *
 * FEATURES:
 * =========
 *
 * HISTORY:
 * ================
 * v1.0
 */

UserClassG2::singleton();

// Required for proper timing with MW class loading.
// Note that we are pushing the initialisation of this class at the top of the stack. 
// Also note that we are not implementing 'stubbing'.
array_unshift( 	$wgExtensionFunctions, 
				create_function('','$GLOBALS["wgUser"] = UserClassG2::loadFromSession();') 
			);

/*  SUPERCLASS DEFINITION
   ***********************/
class UserEx extends ExtensionClass
{
	const thisName = 'UserClassExtendedG2';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version

	public static function &singleton( )
	{ return parent::singleton( ); }

	public function UserEx()
	{ 
		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array(
	    'name'    => self::thisName,
		'version' => '$LastChangedRevision$',
		'author'  => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
		);

		return parent::__construct();	
	}

	function setup()
	{
		parent::setup();
		global $wgHooks;
		$wgHooks['UserCanEx'][] = array( &$this, 'hUserCanEx' );			
	}
	
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		// check directly in the source if the hook is present 
		$userclass = @file_get_contents('includes/user.php');
		
		if (!empty($userclass))
			$r = preg_match('/UserCanEx/si',$userclass);
		
		if ( $r==1 )
			$rresult = '<b>UserCanEx hook operational</b>';
		else
			$rresult = '<b>UserCanEx hook <i>not</i> operational</b>';
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
		{
			if ($el['name']==self::thisName)
				$el['description'].= $rresult;	
		}
	
		return true; // continue hook-chain.
	}


###################################################################################
/*
    New Methods
*/
###################################################################################
	static $gRights = array(
	); 
	
	public function hUserCanEx( $action, )
	{
		
	}
	
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

} # end class definition
?>