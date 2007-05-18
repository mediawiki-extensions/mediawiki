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

	static $base = 'scripts/';
	
	// error code constants
	const msg_nons = 1;
	const msg_folder_not_writable = 2;

	public static function &singleton()
	{ return parent::singleton( );	}
	
	function ScriptsManagerClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( self::$mgwords, $passingStyle, $depth );

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
			self::msg_nons => 'SCRIPTS namespace <b>not</b> declared.',
			self::msg_folder_not_writable => 'Scripts folder can <b>not</b> be written to.',
		);
		
		return $message[ $code ];
	}
	public function hArticleSave( &$article, &$user, &$text, &$summary, $minor, $dontcare1, $dontcare2, &$flags )
	// This hook is used to capture the source file & save it also in the file system.
	{
		// check if we are in the right namespace
		$ns = $article->mTitle->getNamespace();
		if ($ns != NS_SCRIPTS) return true;
		
		// we are in the right namespace,
		// but are we committing to file?
		if (!$this->docommit) return true;
		
		$titre = $article->mTitle->getBaseText();
		
		$r = file_put_contents( self::$base.$titre, $text );
		
		return true; // continue hook-chain.
	}
	
	// public function hUnknownAction( $action, $article )
	/*  This hook is used to implement the custom 'action=commit'
	 */
	
} // END CLASS DEFINITION
?>