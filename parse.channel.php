<?php
/*
	PEAR Channel Update Tool: parses 'channel.xml' file
	Command Line Utility
	@author: Jean-Lou Dupont
	$Id$
	
	This script would usually be called from other scripts in the /REST/ directory.
*/
//<source lang=php>

@require "PEAR/XMLParser.php";

// use a class for namespace management.
class Channel
{
	static $file_name = 'channel.xml';
	static $dir = null;
	static $contents = null;
	static $file = null;
	static $data = null; // parsed file.
	
	// this variable will hold the channel's uri
	static $uri = null;
	
	static function run()
	{
		self::$dir = dirname( __FILE__ );

		self::$file = self::$dir.'/'.self::$file_name;
		
		self::$contents = @file_get_contents( self::$file );
		if (empty( self::$contents ))
			return;
			
		self::$uri = self::parse( self::$contents );
	}
	static function parse( &$contents )
	{
		$parser = new PEAR_XMLParser;
		$result = $parser->parse( $contents );
		if (!$result)
			return false;
		self::$data = $parser->getData();
		
		if (isset(self::$data['name']))
			return self::$data['name'];
			
		return null;
	}
}

Channel::run();
