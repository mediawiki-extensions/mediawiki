<?php
/* 
 * LoadBalancerExtended.php
 *
 * @MediaWiki extension
 * @author: Jean-Lou Dupont
 * www.bluecortex.com
 *
 * PURPOSE:  This extension is a necessary building block required
 * ========  for the support of the 'installation partitioning'
 *           feature.
 *
 *           This extension is meant to be used in conjunction 
 *           with 'DatabaseExtended' extension.
 *
 * IMPLEMENTATION NOTES:
 * =====================
 *
 *           The standard Mediawiki LoadBalancer class builds
 *           on an assumption of the underlying database type
 *           (see getConnection method). This complicates 
 *           database class enhancement.
 *
 *           An additional complication: the heavy reliance on
 *           global variables (e.g. wgDBtype).
 *
 *           During the setup process, the file 'Setup.php' instantiates
 *           a 'LoadBalancer' stub with global parameters found in 'LocalSettings.php'.
 *           In order to enhance the installation with 'wgDBclass' global parameter,
 *           a 'LoadBalancerExtended' object must be instantiated in place of the stub
 *           and must be initialized along with the wgDBclass parameter. This is 
 *           accomplished through the 'wgExtensionFunctions' setup phase (see 'Setup.php').
 *
 * FEATURES:
 * =========
 *  - No code change in the standard Mediawiki package.
 *  - Support for 'extended' MySQL Database class
 *  - Support for '$wgDBclass' (defaults to 'mysql' when wgDBtype=='mysql')
 *    This global parameter complements the set available to describe the database.
 *    This global parameter must be set along with the usual ones (e.g. $wgDBtype etc.)
 *    in 'LocalSettings.php'. 
 *
 * HISTORY:
 * ================
 * - Code borrowed from MW 1.9.3: method 'reallyOpenConnection'
 */


// REQUIRED INCLUDES.
// MW does not load the following in the order we need it
require_once("includes/LoadBalancer.php"); 

// We need to get our extension as high in the initialisation list as possible.
array_unshift( 	$wgExtensionFunctions, 
				create_function('','$GLOBALS["wgLoadBalancer"] = new LoadBalancerEx(); ') 
			);

class LoadBalancerEx extends LoadBalancer
{
	const thisName = 'LoadBalancerEx';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version

	public static function &singleton( )
	{ return parent::singleton( ); }
	
	public function LoadBalancerEx()
	{
		global $wgExtensionCredits;
		
		$wgExtensionCredits['other'][] = array(
		    'name'        => self::thisName,
			'version'     => '$LastChangedRevision$',
			'author'      => 'Jean-Lou Dupont [http://www.bluecortex.com]',
			'description' => 'Extends the standard LoadBalancer class. '
		);

		global $wgHooks;
		$wgHooks['SpecialVersionExtensionTypes'][] = array( &$this, 'hUpdateExtensionCredits' );				
		
		// Let's copy-cat what we can from 'Setup.php' when it initializes
		// the LoadBalancer stub object:
		/*
			$wgLoadBalancer = new StubObject( 'wgLoadBalancer', 'LoadBalancer', 
				array( $wgDBservers, false, $wgMasterWaitTimeout, true ) );
		*/
		// Remember that 'wgDBservers' gets initialized in 'Setup.php'...
		global $wgDBservers, $wgMasterWaitTimeout;
		global $wgDBclass; // our new parameter here.
		
		$wgDBservers[0]['classname'] = $wgDBclass;
		
		return parent::__construct( $wgDBservers, false, $wgMasterWaitTimeout, true );	
	}
###################################################################################
/*
    New Methods
*/
###################################################################################
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits, $wgDBclass, $wgDBtype;
	
		if (!isset( $wgDBclass)) return;
			
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
		{
			if ($el['name']==self::thisName)
				$el['description'].=" \$wgDBtype is set to <b>{$wgDBtype}</b> and \$wgDBclass is set to a <b>{$wgDBclass}</b>.";	
		}
	
		return true; // continue hook-chain.
	}

###################################################################################
/*
    Overloaded Methods
*/
###################################################################################

	function reallyOpenConnection( &$server ) {
		if( !is_array( $server ) ) {
			throw new MWException( 'You must update your load-balancing configuration. See DefaultSettings.php entry for $wgDBservers.' );
		}
echo 'here  ';
		extract( $server );
		
		// ************** BEGIN PATCH ******************
		if (isset( $classname))
			if ( ($type == 'mysql') && (!empty($classname)) )
				$type = $classname;
		// **************  END PATCH  ******************
		
		# Get class for this database type
		$class = 'Database' . ucfirst( $type );

		# Create object
		$db = new $class( $host, $user, $password, $dbname, 1, $flags );
		$db->setLBInfo( $server );
		return $db;
	}

} # end class definition
?>