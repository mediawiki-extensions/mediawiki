<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 */
//<source lang=php>

if (!class_exists('StubManager'))
	echo '[[Extension:ExtensionManager]] <b>requires</b> [[Extension:StubManager]]'."\n";
elseif (version_compare( StubManager::version(), '1.0.0','<' ))
	echo '[[Extension:ExtensionManager]] <b>requires</b> a newer version of [[Extension:StubManager]]'."\n";
else
{
	StubManager::createStub2(	array(	'class' 		=> 'ExtensionManager', 
										'classfilename'	=> dirname(__FILE__).'/ExtensionManager.body.php',
										'hooks'			=> array( ),
										'mgs'			=> array( )
									)
							);
}

/**
 *	Loads all the installed extensions.
 *	
 */
global $IP;
require_once $IP.'/includes/ObjectCache.php';
require_once $IP.'/includes/BagOStuff.php';

class ExtensionLoader
{
	static $expiryPeriod = 86400; //24*60*60 == 1day
	static $realCache = true; // assume we get a real cache.
	static $cache;
	static $edir;			// extensions directory 

	static $exts = null;
	static $installed = null;

	/**
	 */
	static function init()
	{
		self::$cache = & wfGetMainCache();	

		if (self::$cache instanceof FakeMemCachedClient)
			self::$realCache = false;
	}
	/**
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
		Reads all the installed extensions from the filesystem
		
		This function assumes a fixed relative path structure
		e.g.
		/$dirX
		/$dirX/ExtensionManager/ExtensionManager_stub.php
		
		where $dirX is just the parent directory.
		
		Extensions are located in /extensions
	 */
	static function readFromFileSystem()
	{
		self::$edir = realpath( dirname( dirname(__FILE__) ) );
		$dirs = self::getDirs( self::$edir );
		
		// Each directory found is assumed to be an extension.
		return self::extractFiles( $dirs );
	}
	/**
		Assumption: Directory Name --> Extension Name[_stub].php
		
		Return array structure:
		[ extension directory => [ file, disabled flag ] ]
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
	 */
	static function writeToCache( $key, &$exts )
	{
		if (!self::$realCache)
			return false;
			
		$s = serialize( $exts );
		self::$cache->set( $key, $s, self::$expiryPeriod );
	}
	/**
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
	 */
	static function getKey( )
	{
		return '~#ExtensionManager#~';
	}
	static function getInstalled()
	{
		return self::$installed;
	}
	/**
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
// This procedure loads the extensions in the global scope.
// (fix for the ::run function)
$extListe = ExtensionLoader::getList();
if (!empty( $extListe ))
	foreach( $extListe as $extName => $extFileName )
		require( $extFileName);

// Update some fields in [[Special:Version]] page.
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'Extension Manager',
	'version'		=> '1.1.0',
	'author'		=> 'Jean-Lou Dupont',
	'url'			=> 'http://www.mediawiki.org/wiki/Extension:ExtensionManager',	
	'description' 	=> "Provides management of MediaWiki extensions.".
						" Using real cache: " . ExtensionLoader::realCacheStatus().'.', 
);

//</source>