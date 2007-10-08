<?php
/**
 * @author Jean-Lou Dupont
 * @package UserSettingsChangedLog
 * @version $Id$
 */
//<source lang=php>*/
require_once('UserSettingsChangedLog.i18n.php');

class UserSettingsChangedLog
{
	const thisType = 'other';
	const thisName = 'UserSettingsChangedLog';
	
	public function __construct()
	{
		# Add a new log type
		global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgLogTypes[]							= 'usetchglog';
		$wgLogNames  ['usetchglog']				= 'usetchglog'.'logpage';
		$wgLogHeaders['usetchglog']				= 'usetchglog'.'logpagetext';
		$wgLogActions['usetchglog/usetchglog']  = 'usetchglog'.'logentry';
		$wgLogActions['usetchglog/saveok']     	= 'usetchglog'.'-saveok-entry';
		
		global $wgMessageCache;

		$msg = $GLOBALS[ 'msg'.__CLASS__ ];
		
		foreach( $msg as $key => $value )
			$wgMessageCache->addMessages( $msg[$key], $key );		
	}
	public function hUserSettingsChanged( &$user )
	{
		// case 1: new account creation
		// Just bail out.
		global $wgUser;
		if ( $wgUser->getID() == 0 )
			return true;

		// Case 2:
		// we need some protection against multiple saves per transaction.
		// SpecialPreferences.php does multiple saves regularly...
		static $firstTimePassed = false;
		
		if ($firstTimePassed === false)
		{
			$firstTimePassed = true;
			return true;
		}
		
		$title = $user->getUserPage();
		$message = wfMsgForContent( 'usetchglog'.'-save-text', $user->mName );
		
		$log = new LogPage( 'usetchglog' );
		$log->addEntry( 'saveok', $title, $message );

		return true;		
	}
}
//</source>