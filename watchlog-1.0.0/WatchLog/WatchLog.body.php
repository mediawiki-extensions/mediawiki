<?php
/**
 * @author Jean-Lou Dupont
 * @package WatchLog
 */
//<source lang=php>*/
require_once('WatchLog.i18n.php');

class WatchLog
{
	const thisType = 'other';
	const thisName = 'WatchLog';
	
	public function __construct()
	{
		# Add a new log type
		global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgLogTypes[]                        = 'watchlog';
		$wgLogNames  ['watchlog']            = 'watchlog'.'logpage';
		$wgLogHeaders['watchlog']            = 'watchlog'.'logpagetext';
		$wgLogActions['watchlog/sentok']     = 'watchlog'.'-sentok-entry';
		
		global $wgMessageCache;

		$msg = $GLOBALS[ 'msg'.__CLASS__ ];
		
		foreach( $msg as $key => $value )
			$wgMessageCache->addMessages( $msg[$key], $key );
	}
	public function hWatchArticleComplete( &$user, &$article )
	{
		$message = wfMsgForContent( 'watchlog-watch-text', $article->mTitle->getPrefixedText() );
		
		$log = new LogPage( 'watchlog' );
		$log->addEntry( 'watchok', $user->getUserPage(), $message );
		
		return true;
	}
	public function hUnwatchArticleComplete( &$user, &$article )
	{
		$message = wfMsgForContent( 'watchlog-unwatch-text', $article->mTitle->getPrefixedText() );
		
		$log = new LogPage( 'watchlog' );
		$log->addEntry( 'unwatchok', $user->getUserPage(), $message );
		
		return true;
	}
}
//</source>