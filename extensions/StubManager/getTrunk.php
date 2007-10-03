<?php
/**
	Gets the current trunk revision of an extension
	@author Jean-Lou Dupont
	@package MediaWiki
 */

$svn_trunk = 'http://mediawiki.googlecode.com/svn/trunk/extensions';

@require "PEAR/XMLParser.php";
@require 'HTTP/Request.php';

if (!class_exists('PEAR_XMLParser'))
{
	echo 'getTrunk: requires library PEAR/XMLParser. Get it through PEAR.';
	die(0);
}
if (!class_exists('HTTP_Request'))
{
	echo 'getTrunk: requires library HTTP/Request. Get it through PEAR.';
	die(0);
}
 
$extension = $argv[1];
if (empty( $extension ))
{
	echo 'getTrunk: expects extension directory as parameter.'; 
	die(0);
}

$current_dir = @realpath( dirname( __FILE__ ));

$cdir = DirHelper::get( $current_dir );

if ( $cdir !== 'extensions' )
{
	echo "getTrunk: current directory isn't '/extensions' (directory: $cdir) \n";
	
	// try going up one level.
	$current_dir = @realpath( dirname( dirname( __FILE__ ) ) );	
	$cdir = DirHelper::get( $current_dir );
	
	if ( $cdir !== 'extensions' )	
	{
		echo "getTrunk: tried going up one directory level without success\n";	
		die(0);
	}
}

// we'll use this directory for storing the files we 'wget'
$useDir = $current_dir.'/'.$extension.'/';

echo "getTrunk: using target directory: ".$useDir."\n";

/*
if (($type = @filetype($current_dir.'/'.$extension )) === false)
{
	echo 'getTrunk: expects a valid directory as parameter.';
	die(0);
}
if ('dir' !== $type)
{
	echo 'getTrunk: expects a valid directory as parameter.';
	die(0);
}
*/

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

echo 'getTrunk: using base uri: '.$svn_trunk."\n";

$manifest_file = $svn_trunk.'/'.$extension.'/META-INF/manifest.xml';
echo 'getTrunk: fetching manifest file ... ';
$code = getTrunk::get( $manifest_file, $manifest_data );
echo ($code==200) ? "success!\n":"failure! code=".$code."\n";
if ( $code !== 200)
{
	#echo "Response Headers: ";
	#var_dump( getTrunk::$responseHeaders );
	die(0);
}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$files = getTrunk::extractFiles( $manifest_data );

if (empty( $files ))
{
	echo "getTrunk: nothing to do!\n";
	die(0);
}
foreach( $files as $fileEntry )
{
	$file = $fileEntry['file'];
	$uri = $svn_trunk.'/'.$extension.'/'.$file;
	$code = getTrunk::get( $uri, $contents );
	if ( 200 !== $code )
	{
		echo "getTrunk: error fetching file: ".$file."\n";
		continue;
	}
	else
		echo "getTrunk: success for file: ".$file."\n";
	
	$bytes_written = @file_put_contents( $useDir.$file, $contents );
	if ( $bytes_written !== strlen( $contents ))
		echo "getTrunk: error writing file: ".$file."\n";
	
}
echo "getTrunk: completed.\n";
die(1);

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

class getTrunk
{
	static $responseHeaders = null;
	static $responseCode = null;
	
	static function get( $uri, &$document )
	{
		$request =& new HTTP_Request( $uri );
		
		$request->setMethod( "GET" );

		$request->sendRequest();

		// return all response headers.
	    self::$responseHeaders =$request->getResponseHeader();
		$document = $request->getResponseBody();
		self::$responseCode = $code = $request->getResponseCode();
	
		return $code;
	}
	/**
		Extracts the files from the manifest file
	 */
	static function extractFiles( &$m ) 
	{
		$parser = new PEAR_XMLParser;
		$result = $parser->parse( $m );
		if (!$result)
			return false;
		$data = $parser->getData();

		#var_dump( $data );
		if (empty( $data ))
			return null;

		$entries = $data['manifest:file-entry'];
		if (empty( $entries ))
			return null;
		
		$files = array();
		foreach( $entries as &$e )
		{
			$a = $e['attribs'];
			$mime = $a['manifest:media-type'];
			$file = $a['manifest:full-path'];
			
			$files[] = array( 'file'=> $file, 'mime'=> $mime );
		}
		
		return $files;
	}
}
class DirHelper
{
	static function get( $cdirpath )
	{
		// make this work on both Windows & *nix
		$cdirpath = str_replace("\\", "/", $cdirpath );

		$parts = explode( '/', $cdirpath );
		
		$cdir = $parts[count( $parts ) -1 ];

		return $cdir;
	}
}
