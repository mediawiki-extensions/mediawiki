<?php
/*
 * ScriptsManagerClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * 
 */

class ScriptsManagerClass extends ExtensionClass
{
	// constants.
	const thisName = 'ScriptsManager';
	const thisType = 'other';
	  
	const actionName = 'editscripts'; 

	static $base = 'scripts/';
	
	// error code constants
	const msg_nons = 1;
	const msg_folder_not_writable = 2;
	const msg_save_success     = 3;
	const msg_save_not_success = 4;

	public static function &singleton()
	{ return parent::singleton( );	}
	
	function ScriptsManagerClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( );

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.00 $id$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Manages the script files in /home/scripts. '
		);
	}
	public function setup() 
	{ 
		parent::setup();
		
		// Keep this 'true' until I get around to doing
		// the 'commit' functionality.
		$this->docommit = true;

		# Add a new log type
		global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgLogTypes[]                           = 'editscript';
		$wgLogNames['editscript']               = 'editscriptlogpage';
		$wgLogHeaders['editscript']             = 'editscriptlogpagetext';
		$wgLogActions['editscript/editsuccess'] = 'editscriptlog-editsuccess-entry';
		$wgLogActions['editscript/editfail']    = 'editscriptlog-editfail-entry';
		
		global $wgMessageCache, $wgScriptsManagerLogMessages;
		foreach( $wgScriptsManagerLogMessages as $key => $value )
			$wgMessageCache->addMessages( $wgScriptsManagerLogMessages[$key], $key );		
	} 
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits;
		
		// check if the required namespace exists
		if (!defined('NS_SCRIPTS')) { $m = $this->getMessage(self::msg_nons); }
		
		// do we have 'write access' to the scripts folder?
		$r = is_writable( self::$base );
		if (!$r) { $m .= $this->getMessage( self::msg_folder_not_writable ); }
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if ($el['name']==self::thisName)
				$el['description'].=$m;	
		
		return true; // continue hook-chain.
	}
	private function getMessage( $code ) // FIXME: internationalise
	{
		$message = array(
			self::msg_nons                => 'NS_SCRIPTS namespace <b>not</b> declared.',
			self::msg_folder_not_writable => 'Scripts folder can <b>not</b> be written to.',
			self::msg_save_success        => 'Script commit successful',
			self::msg_save_not_success    => 'Script commit <b>not</b> successful',
		);
		
		return $message[ $code ];
	}
	public function hArticleSave( &$article, &$user, &$text, &$summary, $minor, $dontcare1, $dontcare2, &$flags )
	// This hook is used to capture the source file & save it also in the file system.
	{
		// check if we are in the right namespace
		$ns = $article->mTitle->getNamespace();
		if ($ns != NS_SCRIPTS) return true;

		// does the user have the right to edit the scripts?
		// i.e. commit the changes to the file system.
		if (! $article->mTitle->userCan(self::actionName) ) return true;  

		// we are in the right namespace,
		// but are we committing to file?
		if (!$this->docommit) return true;
		
		$titre = $article->mTitle->getBaseText();
		
		$r = file_put_contents( self::$base.$titre, $text );
		
		// write a log entry with the action result.
		$action = ($r === FALSE ? 'editfail':'editsuccess' );		
		$message = wfMsgForContent( 'editscriptlog-edit-text', $user->getName() );
		
		$log = new LogPage( 'editscript' );
		$log->addEntry( $action, $user->getUserPage(), $message );
		
		return true; // continue hook-chain.
	}
	public function hSiteNoticeAfter( &$siteNotice )
	{
		$siteNotice .= $this->result;
		return true;	
	}
	
	// public function hUnknownAction( $action, $article )
	/*  This hook is used to implement the custom 'action=commit'
	 */
	
} // END CLASS DEFINITION
?>