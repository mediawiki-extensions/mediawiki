<?php
/**
 * @author Jean-Lou Dupont
 * @package SmartyAdaptor
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
@require 'Smarty/Smarty.class.php';

class SmartyAdaptor
{
	const thisType = 'other';
	const thisName = 'SmartyAdaptor';

	// constants
	const codeDebug = 0;
	const codeOK    = 1;
	const codeError = 2;
	const codeAllOK = 3;

	static $msg = array();

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
	
	static $allOK = false;
	
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
	 * Service to other extensions
	 *
	 * @param $name name of the template: useful if other extensions hook themselves here
	 * @param $tpl  template path
	 * @param $params array of parameters
	 * @param $result holds the result
	 */
	public function hsmarty( &$name, $tpl, &$params, &$result )
	{
		// means nothing happened
		// also reports if the extension is at least present.
		$result = '';
		
		if (empty( $params ))
			return true;
		
		// one less crashing potential!
		if (!is_file( $tpl ))
			return true;
			
		$smarty = new Smarty();
		
		// the caller *must* supply the full path to the template anyways
		$smarty->template_dir = '.'; 
		$smarty->compile_dir  = self::$compileDir;
		$smarty->cache_dir    = self::$cacheDir;
		
		foreach( $params as $key => &$value )
			$smarty->assign( $key, $value );
		
		$result = $smarty->fetch( $tpl );

		return true;	
	}	
	/**
	 * Hook called during [[Special:Version]]
	 * Used for displaying debug information to the sysop.
	 */	
	public function hSpecialVersionExtensionTypes( &$sp, &$ext )
	{
		global $wgExtensionCredits;
		
		if ( !self::$allOK )
			$result = $this->getDebugInformation();
		else
			$result = wfMsgForContent( 'smartyadaptor'.SmartyAdaptor::codeAllOK ); 
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (@isset($el['name']))
				if ($el['name'] == self::thisName)
					$el['description'] .= $result.'.';
		
		return true;
	}
	/**
	 * Formats the debug information
	 */	
	protected function getDebugInformation()
	{
		// Smarty available: $1, 
		// Cache Directory available: $2
		// Compile Directory available: $3
		// Cache Directory Writable: $4
		// Compile Directory Writable: $5
		
		$ok  = wfMsgForContent( 'smartyadaptor'.self::codeOK );
		$err = wfMsgForContent( 'smartyadaptor'.self::codeError );		
		
		$p1 = self::$smartyExists     ? $ok:$err;
		$p2 = self::$cacheDirExists   ? $ok:$err;
		$p3 = self::$compileDirExists ? $ok:$err;
		$p4 = self::$cacheDirW        ? $ok:$err;
		$p5 = self::$compileDirW      ? $ok:$err;								
		
		return wfMsgForContent( 'smartyadaptor'.self::codeDebug, 
								$p1, $p2, $p3, $p4, $p5 );
	}
	/**
	 * Gets useful debug information.
	 */	
	protected static function getInformation()
	{
		// do the directories exist?
		$r  = self::$cacheDirExists   = is_dir( self::$cacheDir );
		$r &= self::$compileDirExists = is_dir( self::$compileDir );	
		
		// are they writable?
		$r &= self::$cacheDirW   = is_writable( self::$cacheDir );
		$r &= self::$compileDirW = is_writable( self::$compileDir );
		
		$r &= self::$smartyExists = class_exists( 'Smarty' );
		
		self::$allOK = $r;
	}
	
	
} // END CLASS DEFINITION
require 'SmartyAdaptor.i18n.php';
//</source>