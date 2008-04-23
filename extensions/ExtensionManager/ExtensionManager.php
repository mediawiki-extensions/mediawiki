<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 * @category ExtensionManager
 * @version @@package-version@@
 */
//<source lang=php>

require_once $IP.'/includes/ObjectCache.php';
require_once $IP.'/includes/BagOStuff.php';
require_once 'ExtensionBaseClass.php';
require_once 'ExtensionHelperClass.php';

class ExtensionLoader
{
	static $expiryPeriod = 86400; //24*60*60 == 1day
	static $realCache = true; // assume we get a real cache.
	static $cache;
	static $edir;			// extensions directory 

	static $exts = null;
	static $installed = null;

	/**
	 * Initialization
	 */
	private static function init()
	{
		clearstatcache();
		
		self::$cache = & wfGetMainCache();	

		if (self::$cache instanceof FakeMemCachedClient)
			self::$realCache = false;
	}
	/**
	 * Retrieves the list of registered extensions
	 * either through the cache or filesystem
	 */
	static function getExtensions()
	{
		// first, check the cache
		$key = self::getKey();
		$cached = self::readFromCache( $key );

		if (!empty( $cached ))
			return $cached;
			
		// if nothing is in the cache, then we must
		// build the list... hopefully a cache
		// entry will get created... of course this
		// requires a cache to be configured in
		// LocalSettings.php !				
		$exts = self::readFromFileSystem();
		
		self::writeToCache( $key, $exts );
		
		return $exts;
	}
	/**
	 * Reads all the installed extensions from the filesystem
	 * This function assumes a fixed relative path structure
	 * e.g.
	 * 	/$dirX
	 * 	/$dirX/ExtensionManager/ExtensionManager_stub.php
	 * 
	 * 	where $dirX is just the parent directory.
	 * 
	 * 	Extensions are located in /extensions
	 */
	static function readFromFileSystem()
	{
		self::$edir = realpath( dirname( dirname(__FILE__) ) );
		$dirs = self::getDirs( self::$edir );
		
		// Each directory found is assumed to be an extension.
		return self::extractFiles( $dirs );
	}
	/**
	 * Assumption: Directory Name --> Extension Name[_stub].php
	 * 
	 * 	Return array structure:
	 * 	[ extension directory => [ file, disabled flag ] ]
	 */
	static function extractFiles( &$dirs )
	{
		$files = null;
		
		if (empty( $dirs ))	
			return null;

		$path = self::$edir;		
		foreach( $dirs as $dir )
		{
			// of course, skip ourself!
			if ($dir == 'ExtensionManager')
				continue;

			// ... and StubManager !!
			if ($dir == 'StubManager')
				continue;
			
			// check for '.disable' directive
			$d = $path.'/'.$dir.'/.disable';
			$disabled = file_exists( $d );
			
			// case 1: $extension_stub.php
			$file1 = $path.'/'.$dir.'/'.$dir.'_stub.php';
			$r1 = file_exists( $file1 );	
			// case 2: $extension.php
			$file2 = $path.'/'.$dir.'/'.$dir.'.php';
			$r2 = file_exists( $file2 );	
		
			$win = null;
			
			// case #1 wins
			if ( $r1 === true )
				$win = $file1;

			// only if case #1 fails that we accept case #2
			if ( ( $r2 === true ) && ($win === null))
				$win = $file2;

			if (!empty( $win ))
				$files[$dir] = array(	'file' => $win, 
										'disabled' => $disabled ); 
		}
		return $files;
	}
	/**
	 * Retrieves the list of directories from
	 * a base directory
	 */
	static function getDirs( &$cdir )
	{
		$files = @scandir( $cdir );
		
		// get rid of the . and .. entries
		$dirs = null;
		foreach( $files as &$file )
		{
			if ( ( '.' == $file ) || ( '..' == $file ))
				continue;
			if ( substr($file,0,1) === '.' )
				continue;
				
			// make sure we have a directory
			$path = $cdir."/".$file;
			$info = @filetype( $path );
			if ( 'dir' !== $info )
				continue;
				
			$dirs[] = $file;
		}
		
		return $dirs;
	}
	/**
	 * Writes the list of extensions to the cache
	 */
	static function writeToCache( $key, &$exts )
	{
		if (!self::$realCache)
			return false;
			
		$s = serialize( $exts );
		self::$cache->set( $key, $s, self::$expiryPeriod );
	}
	/**
	 * Reads the list of extensions from the cache
	 */
	static function readFromCache( $key )
	{
		if (!self::$realCache)
			return false;
		
		$s = self::$cache->get( $key );
		$us = @unserialize( $s );
		
		return $us;
	}
	/**
	 * Flushes the entries from the cache
	 */
	static function flushCache()
	{
		static $flushed = false;
		if ( $flushed )
			return true;
			
		if (!self::$realCache)
			return false;
			
		self::$cache->delete( self::getKey() );		
		
		$flushed = true;
		return true;
	}
	/**
	 * Builds the cache key
	 */
	static function getKey( )
	{
		return '~#ExtensionManager#~';
	}
	/**
	 * Returns the list of installed extensions
	 */
	static function getInstalled()
	{
		return self::$installed;
	}
	/**
	 * Verifies if a 'real' cache is available
	 */
	static function realCacheStatus()
	{
		return (self::$realCache ? 'true':'false');
	}
	/**
	 * Loads the active extensions.
	 *
	 * Note that this function loads the extension whilst
	 * not using the 'global scope' i.e. the 'require' statement
	 * isn't located in the global scope.
	 * Some extensions may break because of this, specifically
	 * the ones not relying on the 'global' statement to declare
	 * variable scope explicitly.
	 */
	static function run()
	{
		self::init();
		
		self::$exts = self::getExtensions();
		
		if (!empty( self::$exts ))
			foreach( self::$exts as $ext => &$e)
			{
				$file = $e['file'];
				$disabled = $e['disabled'];
				if (!$disabled)
				{
					#echo "Installing: ".$ext."\n<br/>";
					self::$installed[] = $ext;
					require( $file );
				}
			}
	} // run
	/**
	 * Returns the list of extensions to load
	 * This list does not include the ones disabled.
	 */
	static function getList()
	{
		self::init();
		
		self::$exts = self::getExtensions();
		
		$liste = array();
		
		if (!empty( self::$exts ))
			foreach( self::$exts as $ext => &$e)
				if (!$e['disabled'])
					$liste[ $ext ] = $e['file'];
		return $liste;
	} // getList
}

// ================================================================
// This procedure loads the extensions in the global scope.
// (fix for the ::run function)
$extListe = ExtensionLoader::getList();
if (!empty( $extListe ))
	foreach( $extListe as $extName => $extFileName )
	{
		$status = include $extFileName;
		
		// help flush uninstalled extensions in a somewhat nice way.
		if ( false === $status )
			ExtensionLoader::flushCache();
	}

// ================================================================
// Update some fields in [[Special:Version]] page.
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'Extension Manager',
	'version'		=> '@@package-version@@',
	'author'		=> 'Jean-Lou Dupont',
	'url'			=> 'http://www.mediawiki.org/wiki/Extension:ExtensionManager',	
	'description' 	=> "Provides management of MediaWiki extensions.".
						" Using real cache: " . ExtensionLoader::realCacheStatus().'.', 
);

//</source>