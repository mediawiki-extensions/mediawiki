<?php
/*<!--<wikitext>-->
This file is part of the extension [[Extension:ExtensionManager]].
<!--</wikitext>--><source lang=php>*/

if (!class_exists('StubManager'))
	echo '[[Extension:ExtensionManager]] <b>requires</b> [[Extension:StubManager]]'."\n";
elseif (version_compare( StubManager::version(), '1.0.0','<' ))
	echo '[[Extension:ExtensionManager]] <b>requires</b> a newer version of [[Extension:StubManager]]'."\n";
else
{
	StubManager::createStub2(	array(	'class' 		=> 'ExtensionManager', 
										'classfilename'	=> dirname(__FILE__).'/ExtensionManager.php',
										'hooks'			=> array( ),
										'mgs'			=> array( )
									)
							);

	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'Extension Manager',
		'version'		=> '1.0.0',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:ExtensionManager',	
		'description' 	=> "Provides management of MediaWiki extensions.", 
	);
}

/**
 *	Loads all the installed extensions.
 *	
 */
class ExtensionLoader
{
	static $expiryPeriod;
	static $realCache = true; // assume we get a real cache.
	static $cache;
	static $edir;			// extensions directory 

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
		
				
				
	}
	/**
	 */
	static function formatArray()
	{
		
	}
	/**
		Reads all the installed extensions from the filesystem
		
		This function assumes a fixed relative path structure
		e.g.
		/extensions
		/extension/ExtensionManager/ExtensionManager_stub.php  
		
		Extensions are located in /extensions
	 */
	static function readFromFileSystem()
	{
		self::$edir = realpath( dirname( dirname(__FILE__) ) );
		$dirs = self::getDirs( self::$edir );
		
		// Each directory found is assumed to be an extension.
		$files = self::extractFiles( $dirs );
		if (empty( $files ))
			return null;
			
	}
	/**
		Assumption: Directory Name --> Extension Name[_stub].php
		
		Return array structure:
		[ extension directory => file to load ]
	 */
	static function extractFiles( &$dirs )
	{
		$files = null;
		
		if (empty( $dirs ))	
			return null;

		$path = self::$edir;		
		foreach( $dirs as $dir )
		{
			// case 1: $extension_stub.php
			$file1 = $path.'/'.$dir.'/'.$dir.'_stub.php';
			$r1 = file_exists( $file1 );	
			// case 2: $extension.php
			$file2 = $path.'/'.$dir.'/'.$dir.'.php';
			$r2 = file_exists( $file2 );	
		
			// case #1 wins
			if ( $r1 === true )
				{ $files[$dir] = $files1; continue; }

			// only if case #1 fails that we accept case #2
			if ( $r2 === true )
				$files[$dir] = $files2;
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
		
		$s = self::$realCache->get( $key );
		$us = @unserialize( $s );
		
		return $us;
	}
	/**
	 */
	static function getKey( $key )
	{
		return ;
	}
	/**
	 */
	static function run()
	{
		self::init();
		
		$exts = self::getExtensions();

		if (!empty( $exts ))
			foreach( $exts as $extFilePath)
				@require( $extFilePath );
	}
}
ExtensionLoader::run();
//</source>