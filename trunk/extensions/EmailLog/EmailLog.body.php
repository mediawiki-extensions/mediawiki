<?php
/**
 * @author Jean-Lou Dupont
 * @package EmailLog
 * @version $Id$
 */
//<source lang=php>
require_once('EmailLog.i18n.php');

class EmailLog
{
	const thisType = 'other';
	const thisName = 'EmailLog';
	
	public function __construct()
	{
		# Add a new log type
		global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgLogTypes[]                        = 'emaillog';
		$wgLogNames  ['emaillog']            = 'emailloglogpage';
		$wgLogHeaders['emaillog']            = 'emailloglogpagetext';
		$wgLogActions['emaillog/sentok']     = 'emaillog-sentok-entry';
		
		global $wgMessageCache;

		$msg = $GLOBALS[ 'msg'.__CLASS__ ];
		
		foreach( $msg as $key => $value )
			$wgMessageCache->addMessages( $msg[$key], $key );		
	}
	public function hEmailUserComplete( $to, $from, $subject, $text )
	{
		global $wgUser;
		
		$toname = $to->name;
		$fromname = $from->name;
		
		$message = wfMsgForContent( 'emaillog-sent-text', $fromname, $toname );
		
		$log = new LogPage( 'emaillog' );
		$log->addEntry( 'sentok', $wgUser->getUserPage(), $message );
		
		return true;
	}	
}

//</source>