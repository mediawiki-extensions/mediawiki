<?php
/*
 * SmartyAdaptorClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 */

@require 'Smarty/Smarty.class.php';

class SmartyAdaptor
{
	const thisType = 'other';
	const thisName = 'Smarty';

	// constants
	const codeDebug = 0;
	const codeOK    = 1;
	const codeError = 2;

	// directories that *must* be writable & accessible
	// by MediaWiki / WEB server
	const tplCacheDir   = '/smarty/cache';	
	const tplCompileDir = '/smarty/compile';
	
	static $cacheDir   = null;
	static $compileDir = null;
	
	static $cacheDirExists   = false;
	static $compileDirExists = false;	

	static $cacheDirW   = false;
	static $compileDirW = false;
	
	static $smartyExists = false;
	
	/**
	 * Initialize the messages
	 */
	public function __construct()
	{
		global $IP;
		self::$cacheDir   = $IP.self::tplCacheDir;
		self::$compileDir = $IP.self::tplCompileDir;		
		
		self::getInformation();
		
		global $wgMessageCache;

		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}	 
	/**
	 * Hook called during [[Special:Version]]
	 */	
	public function hUpdateExtensionCredits( &$sp, &$ext )
	{
		global $wgExtensionCredits;
		
		$result = $this->getDebugInformation();
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (@isset($el['name']))
				if ($el['name'] == self::thisName)
					$el['description'] .= $result.'.';
		
		return true;
	}
	/**
	 * Formats debug information
	 */	
	protected function getDebugInformation()
	{
		// Smarty available: $1, 
		// Cache Directory available: $2
		// Compile Directory available: $3
		// Cache Directory Writable: $4
		// Compile Directory Writable: $5
		return wfMsgForContent( 'smarty'.self::codeDebug, 
								self::$smartyExists,
								self::$cacheDirExists,
								self::$compileDirExists,
								self::$cacheDirW,
								self::$compileDirW );	
	}
	/**
	 * 
	 */	
	protected static function getInformation()
	{
		// do the directories exist?
		self::$cacheDirExists   = is_dir( self::$cacheDir );
		self::$compileDirExists = is_dir( self::$compileDir );	
		
		// are they writable?
		self::$cacheDirW   = is_writable( self::$cacheDir );
		self::$compileDirW = is_writable( self::$compileDir );
		
		self::$smartyExists = class_exists( 'Smarty' );
	}
	
	
} // END CLASS DEFINITION
require 'Smarty.i18n.php';
//</source>