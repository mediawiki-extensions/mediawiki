<?php
/**
 * @author Jean-Lou Dupont
 * @package NewUserEmailNotification.php
 * @version $Id$
 */
require_once( 'NewUserEmailNotification.i18n.php');
		
class NewUserEmailNotification
{
	const thisType = 'hook';
	const thisName = 'NewUserEmailNotification';
	
	public function __construct()
	{
		global $wgMessageCache;
		global $IP;
		
		require_once( $IP.'/includes/UserMailer.php' );

		$msg = $GLOBALS[ 'msg'.__CLASS__ ];
		
		foreach( $msg as $key => $value )
			$wgMessageCache->addMessages( $msg[$key], $key );		
	}
	public function hAddNewAccount( &$user )
	{
		// Compatibility with old versions which didn't pass the parameter		
		global $wgUser;		
		if( is_null( $user ) )
			$user = $wgUser;

		// hopefully, this global is set!
		global $wgEmergencyContact;
		if (!isset($wgEmergencyContact))
			return true;

		// Use the site name as 'name'
		global $wgSitename;

		$this->sendMail( $user, $wgEmergencyContact, $wgSitename );
		
		return true;
	}

	private function sendMail( $from_user, $to_address, $to_name ) 
	{
		global $wgSitename;
		
		$subject = wfMsg('newuseremailnotification-subject', $wgSitename);
		$body    = wfMsg('newuseremailnotification-body', $wgSitename, $from_user->mName, $from_user->mRealName   );
		
		$to = 		new MailAddress( $to_address, $to_name );
		$sender =	new MailAddress( $from_user );
		$error =	userMailer( $to, $sender, $subject, $body );
	}

} // end class definition.
//</source>