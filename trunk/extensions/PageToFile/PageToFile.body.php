<?php
/**
 * @author Jean-Lou Dupont
 * @package PageToFile
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
require( 'PageToFile.i18n.php' );

class PageToFile
{
	// constants.
	const thisName = 'PageToFile';
	const thisType = 'other';
	  
	const actionCommit = 'commitpage';

	function __construct()
	{
		# Add a new log type
		global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgLogTypes[]                          = 'commitfil';
		$wgLogNames  ['page2file']             = 'commitfil'.'logpage';
		$wgLogHeaders['page2file']             = 'commitfil'.'logpagetext';
		$wgLogActions['page2file/commitok']	   = 'commitfil'.'-commitok-entry';
		$wgLogActions['page2file/commitfail']  = 'commitfil'.'-commitfail-entry';		
		$wgLogActions['page2file/commitfail2'] = 'commitfil'.'-commitfail2-entry';				
		
		global $wgMessageCache;

		$msg = $GLOBALS[ 'msg'.__CLASS__ ];
		
		foreach( $msg as $key => $value )
			$wgMessageCache->addMessages( $msg[$key], $key );		
	}
	public function hArticleSave( &$article, &$user, &$text, &$summary, $minor, $dontcare1, $dontcare2, &$flags )
	// This hook is used to capture the source file & save it also in the file system.
	{
		// check if we are in the right namespace
		$ns = $article->mTitle->getNamespace();
		if ($ns != NS_PAGEFILE) 
			return true;

		$pageTitle = $article->mTitle->getText();

		// does the user have the right to commit scripts?
		// i.e. commit the changes to the file system.
		if (! $article->mTitle->userCan(self::actionCommit) ) 
			return true;  

		if (! $this->verifyPageTitle( $pageTitle ) )
		{
			$this->logPageTitleError( $pageTitle );
			return true;
		}

		$r = $this->doCommit( $pageTitle, $text );
		
		// write a log entry with the action result.
		// -----------------------------------------
		$action  = ($r === FALSE) ? 'commitfail':'commitok';
		$message = wfMsgForContent( 'page2file-commit-text', $nsname, $titre, $pageTitle );
				
		$this->logCommit();
		
		// disable auto summary
		// (security issue ...)
		$flags = ($flags & (~EDIT_AUTOSUMMARY));
		
		return true; // continue hook-chain.
	}
	/**
	 * Verifies that the page title does not contain directory separators.
	 */
	private function verifyPageTitle( &$pageTitle )
	{
		return strpos( $pageTitle, DIRECTORY_SEPARATOR ) === false;
	}
	/**
	 *
	 */
	private function logPageTitleError( )
	{
		$action  = 'commitfail';
		$message = wfMsgForContent( 'page2file-commit-text' );
		$this->doLog( $action, $message );
	}
	/**
	 *
	 */
	private function logCommit( $action, $messageId )
	{
		$message = wfMsgForContent( $messageId );
		$this->doLog( $action, $message );
	}
	/**
	 *
	 */
	private function doLog( $action, $message )
	{
		global $wgUser;
		
		// we need to limit the text to 'page2file' because of the database schema.
		$log = new LogPage( 'page2file' );
		$log->addEntry( $action, $wgUser->getUserPage(), $message );
	}
	
	/**
	 *
	 */
	private function doCommit( &$pageTitle, &$text )
	{
		global $wgUploadDirectory;
		$fileName = $wgUploadDirectory.'/'.$pageTitle;

		$bytes_written = @file_put_contents( $fileName, $text );
		return ($bytes_written !== strlen( $text ));
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%



} // END CLASS DEFINITION
//</source>