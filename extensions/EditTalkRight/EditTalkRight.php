<?php
/**
 * @author Jean-Lou Dupont
 * @package Enforces the 'edit_talk' right
 * @category PermissionManagement
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>

class MW_EditTalkRight {

	protected function __construct(){}
	
	/**
	 * Register the extension
	 */
	public static function init() {
		global $wgExtensionFunctions;
		$wgExtensionFunctions[] = array( __CLASS__, 'setup' );
	}	
	/**
	 * Sets hook and credit
	 */
	public static function setup() {
		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => 'EditTalkRight', 
			'version'     => '@@package-version@@',
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Enforces the "edit_talk" right',
			'url' 		  => 'http://mediawiki.org/wiki/Extension:EditTalkRight',			
		);

		global $wgHooks;
		$wgHooks['UserGetRights'][] = array( new MW_EditTalkRight, "onUserGetRights" );
	}
	/**
	 * Hook 'UserGetRights'
	 * 
	 * @param $user Object
	 * @param $rights array
	 */
	public static function onUserGetRights( &$user, &$rights ) {

		global $wgTitle;
		
		// paranoia
		if ( !is_object( $wgTitle ) )
			return true;
			
		// furthermore, only care about the 'talk' namespaces
		if ( !$wgTitle->isTalkPage() )
			return true;
		
		// add the 'edit' right if the current operation
		// is performed in a 'talk' namespace && the user has the 'edit_talk' right
		if ( in_array( 'edit_talk', $rights ) ) {
		
			$rights[] = 'edit';
			$rights[] = 'createtalk';
		}
				
		return true;		
	}
	
} //end class

MW_EditTalkRight::init();

//</source>