<?php
/**
 * @author Jean-Lou Dupont
 * @package PageRestrictions
 * @version @@package-version@@
 * @Id $Id$
*/
//<source lang=php>
class PageRestrictions
{
	// constants.
	const thisName = 'PageRestrictions';
	const thisType = 'other';
	const id       = '$Id$';	

	function __construct( ) {}
	/**
	 */
	public static function addRestrictionLevels( &$l = null )
	{
		global $wgRestrictionLevels;
		
		if (!is_array( $l ))
			$l = array( $l );
			
		if (!empty( $l ))
			foreach( $l as $index => $rest )
				$wgRestrictionLevels[] = $rest;
	}
	/**
	 * Main Hook
	 */
	public function hArticleViewHeader( &$a )
	{
		global $wgUser;
		global $action;

		// some rewrite required...
		if ($action == 'view')		
			$action = 'read';
			
		if ( !$wgUser->isAllowed( $action ) )
			self::accessError(); // dies here.
		
		return true;
	}
	private static function accessError()
	{
		global $wgOut;
		$wgOut->setPageTitle( wfMsg( 'badaccess' ) );
		$wgOut->addWikiText( wfMsg( 'badaccess-group0' ) );
		$wgOut->output();
		exit();
	}
} // end class declaration
//</source>