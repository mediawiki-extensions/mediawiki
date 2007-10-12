<?php
/**
 * @author Jean-Lou Dupont
 * @package PageRestrictions
 * @version $Id$
*/
//<source lang=php>
class PageRestrictions
{
	// constants.
	const thisName = 'PageRestrictions';
	const thisType = 'other';
	const id       = '$Id$';	

	static $msg = array();

	function __construct( )
	{
		self::loadMessages();
	}
	private static function loadMessages()
	{
		global $wgMessageCache;
		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}
	public static function addRestrictionLevels( &$l = null )
	{
		global $wgRestrictionLevels;
		
		if (!is_array( $l ))
			$l = array( $l );
			
		if (!empty( $l ))
			foreach( $l as $index => $rest )
				$wgRestrictionLevels[] = $rest;
	}

	public function hArticleViewHeader( &$a )
	{
		global $wgUser;
		global $action;
		
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
require('PageRestrictions.i18n.php');	

//</source>