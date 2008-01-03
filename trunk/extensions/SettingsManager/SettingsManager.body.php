<?php
/**
 * @author Jean-Lou Dupont
 * @package SettingsManager
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
require_once('SettingsManager.i18n.php');

class SettingsManager
{
	const thisType = 'parser';
	const thisName = 'SettingsManager';
	
	static $reqGroup = 'sysop';
	static $iNs;
	
	// Registry Page
	static $rPage = 'MediaWiki:Registry/Settings';
	
	// filename of wikitext based special page
	static $spFilename = null;
	
	// filename containing the declaration of the managed namespaces
	static $mnName = null;

	// Template page
	static $templatePageName;
	
	// update flag
	var $canUpdateFile;

	var $started = false;
	var $done = false;

	public function __construct() 
	{ 
		self::$spFilename = dirname(__FILE__).'/SettingsManager.specialpage.wikitext';
		self::$mnName = dirname(__FILE__).'/SettingsManager.settings.php';
		self::$templatePageName = dirname(__FILE__).'/settingsmanager.namespaces.template';
		
		// help the user a bit by making sure
		// the file is writable when it comes to update it.
		@chmod( self::$mnName, 0644 );
		
		$this->canUpdateFile = true;
		
		// Log related
		global $wgLogTypes, $wgLogNames, $wgLogHeaders, $wgLogActions;
		$wgLogTypes[]                    = 'mngs';
		$wgLogNames  ['mngs']            = 'mngslogpage';
		$wgLogHeaders['mngs']            = 'mngslogpagetext';
		$wgLogActions['mngs/updtok']	 = 'mngs'.'-updtok-entry';
		$wgLogActions['mngs/updtfail1']  = 'mngs'.'-updtfail1-entry';		
		$wgLogActions['mngs/updtfail2']  = 'mngs'.'-updtfail2-entry';				
		$wgLogActions['mngs/updtfail3']  = 'mngs'.'-updtfail3-entry';
		$wgLogActions['mngs/updtfail4']  = 'mngs'.'-updtfail4-entry';		
		
		// Messages.
		global $wgMessageCache;

		$msg = $GLOBALS[ 'msg'.__CLASS__ ];
		
		foreach( $msg as $key => $value )
			$wgMessageCache->addMessages( $msg[$key], $key );		
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 * var = value
	 * var = array( v1, v2 ... )
	 * var = array( k1=>v1, k2=>v2 ... )
	 *
	 * Q: allow PHP code?
	 *
	 * {{#setting:global-variable-name | value }}
	 * {{#setting:global-variable-name | v1, v2, ... }}	 
	 * {{#setting:global-variable-name | k1=v1, v2, ... }}	 	 
	 */
	public function mg_setting( &$parser, 
							$index = null, 			// numerical index
							$name = null, 			// text
							$identifier = null, 	// text
							$separator = '||' )
	{
		$this->started = true;
		
		// Make sure that this parser function is only used
		// on the allowed registry page
		if (!$this->checkRegistryPage( $parser->mTitle))
			{ return wfMsg('settingsmanager'.'-incorrect-page'); }
		
		// Also make sure that the user has the appropriate right
		if (!$this->checkRight())
			{ return wfMsg('settingsmanager'.'-insufficient-right'); }

		// perform validations only once because
		// in some configurations, the parser is called multiple times		
		if (!$this->done)		
		{
			// Perform validations
			// relative to the Immutable Namespaces
			if (!$this->validateIndex( $index, $msg ))
				{ $index = $msg; $this->canUpdateFile = false; }
				
			if (!$this->validateName( $name, $msg ))
				{ $name = $msg;  $this->canUpdateFile = false; }
	
			if (!$this->validateIdentifier( $identifier, $msg ))
				{ $identifier = $msg;  $this->canUpdateFile = false; }
	
			// Perform validations
			// relative to the defined ones on this page
			if (!$this->validateIndexDefined( $index, $msg ))
				{ $index = $msg; $this->canUpdateFile = false; }
				
			if (!$this->validateNameDefined( $name, $msg ))
				{ $name = $msg;  $this->canUpdateFile = false; }
	
			if (!$this->validateIdentifierDefined( $identifier, $msg ))
				{ $identifier = $msg;  $this->canUpdateFile = false; }

			// at this point, just accumulate the requested changes	
			if ($this->canUpdateFile)
				$this->nsMap[$index] = array( 'name' => $name, 'identifier' => $identifier );
		}
		
		// return the wikitext line
		return $index.$separator.$name.$separator.$identifier;
	}
	/**
	 * This method serves as 'trap' for the file update process.
	 */
	public function hParserAfterTidy( &$parser, &$text )
	{
		if ($this->done)
			return true;
			
		// just trap events related to the registry page in question here
		if ( !$this->checkRegistryPage( $parser->mTitle ) )
			return true;
		
		// .. and of course make sure the user has the required right
		if (!$this->checkRight())
			return true;

		if ($this->started)
			$this->done = true;
		else
			return true;
			
		if (!$this->canUpdateFile())
			$action = 'updtfail3';
		else
			$this->updateFile( $action, $contents );

		$this->updateLog( $action );

		return true;	
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// VALIDATATION & LOGGING

	protected function updateLog( $action  )
	{
		global $wgUser;
		$log = new LogPage( 'mngs' );
		$log->addEntry( $action, $wgUser->getUserPage(), wfMsg('mngs-'.$action.'-text') );
	}
	protected function canUpdateFile() 
	{ 
		return $this->canUpdateFile; 
	}
	protected function checkRegistryPage( &$object )
	{
		if ($object instanceof Title)
			return (($object->getFullText() == self::$rPage) ? true:false );
		return (( $object->mTitle->getFullText() == self::$rPage ) ? true:false);	
	}
	protected function checkRight()
	{
		global $wgUser;
		return in_array( self::$reqGroup, $wgUser->getEffectiveGroups());
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	private function updateFile( &$action, &$contents )
	{
		// check first if the target file is writable
		if (!is_writable( self::$mnName ))
		{
			$action = 'updtfail4'; 
			return false; 
		}
		
		// read the 'template' file
		$template = $this->readFile( self::$templatePageName );
		if ($template === false)
		{ 
			$action = 'updtfail1'; 
			return false; 
		}
		
		// build table.
		$contents = wfMsg( 'settingsmanager'.'-open-code' );
		foreach( $this->nsMap as $index => &$e )
			$contents .= wfMsg( 'settingsmanager'.'-entry-code', $index, $e['name'] );
		$contents .= wfMsg( 'settingsmanager'.'-close-code' );
		
		// defines.
		$contents .= wfMsg( 'settingsmanager'.'-open-code2' );
		foreach( $this->nsMap as $index => &$e )
			$contents .= wfMsg( 'settingsmanager'.'-entry-code2', $index, $e['identifier'] );
		$contents .= wfMsg( 'settingsmanager'.'-close-code2' );

		// do the substitution in the template		
		$code = $this->fillTemplate( $template, $contents );
		
		$len = strlen( $code );
		$put_len = file_put_contents( self::$mnName , $code, LOCK_EX );	
		chmod( self::$mnName, 0644 );
		if ( $put_len !== $len )
		{ 
			$action = 'updtfail2';
			return false; 
		}
		
		$action = 'updtok';

		return true;
	}
	/**
	 *
	 */
	private function readFile( $fn )
	{
		return file_get_contents( $fn );
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 *
	 */
	protected function fillTemplate( &$template, &$code )
	{
		return str_replace('$contents$', $code, $template );
	}

} // end class
//</source>