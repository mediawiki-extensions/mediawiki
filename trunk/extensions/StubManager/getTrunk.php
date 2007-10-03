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
/*
$current_dir = realpath( dirname( __FILE__ ));
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
echo 'getTrunk: using base uri: '.$svn_trunk."\n";

$manifest_file = $svn_trunk.'/'.$extension.'/META-INF/manifest.xml';
echo 'getTrunk: fetching manifest file: '.$manifest_file." ...";
$code = getTrunk::get( $manifest_file, $manifest_data );
echo ($code==200) ? "success!\n":"failure! code=".$code."\n";
if ( $code !== 200)
{
	echo "Response Headers: ";
	var_dump( getTrunk::$responseHeaders );
	die(0);
}
class getTrunk
{
	static $responseHeaders = null;
	static $responseCode = null;
	
	static function get( $uri, &$document )
	{
		$request =& new HTTP_Request( $uri );
		
		$request->setMethod( $verb );

		$request->sendRequest();

		// return all response headers.
	    self::$responseHeaders =$request->getResponseHeader();
		$document = $request->getResponseBody();
		self::$responseCode = $code = $request->getResponseCode();
	
		return $code;
	}
}