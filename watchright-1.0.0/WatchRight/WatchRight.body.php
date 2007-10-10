<?php
/**
 * @author Jean-Lou Dupont
 * @package WatchRight
 */
//<source lang=php>*/
class WatchRight
{
	const thisName = 'WatchRight';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	public function __construct() {}
	
	public function hWatchArticle( &$user, &$article )
	{
		if (!$user->isAllowed( 'watch' ))
			return $this->error( 'watch' );
		return true;
	}

	public function hUnwatchArticle( &$user, &$article )
	{
		if (!$user->isAllowed( 'unwatch' ))
			return $this->error( 'unwatch' );
		return true;			
	}
	private function error( $msg )
	{
		global $wgOut;
	
		$wgOut->addWikiText( wfMsg( 'badaccess' ) );
		
		return false;
	}
	
	public function hSkinTemplateTabs( &$st , &$content_actions )
	{
		global $wgUser;
		
		if (!$wgUser->isAllowed( 'watch') )
			unset( $content_actions['watch'] );

		if (!$wgUser->isAllowed( 'unwatch') )
			unset( $content_actions['unwatch'] );

		return true;
	}
} // end class definition.
//</source>