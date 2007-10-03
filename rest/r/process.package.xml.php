<?php
/*
	Updates the 'date' and 'time' tags
	a valid 'package.$version.xml' file +
	generates the 'deps.$version.txt' file related 
	to the package.

	@author: Jean-Lou Dupont
	
	Aptana:  "${project_loc}/rest/r/update.datetime.package.xml.php"  "${resource_loc}"
	
	- Execute the above Aptana/Eclipse 'external tools' command using PHP.
	- Select target resource e.g. package.0.1.0.xml
*/

if (!isset( $argv[1]))
{
	echo "Requires the filename of the package.xml file!\n";	
	die(0);
}

// grab the source file from the command line
$source_file_name = basename( $argv[1],".xml" );

$cdir = dirname( $argv[1] );

echo 'Source directory: '.$cdir."\n";
echo 'Source file name: '.$source_file_name."\n";

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$file_contents = @file_get_contents( $cdir.'/'.$source_file_name.".xml" );
if (empty( $file_contents ))
{
	echo 'File Empty or invalid file name!'."\n";
	die(0);
}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$p_date = '/\<'.'date\>'.'.*'.'\<\/date\>'.'/siU';
$p_time = '/\<'.'time\>'.'.*'.'\<\/time\>'.'/siU';

$date = '<'.'date'.'>'.gmdate("Y-m-d").'</date'.'>';
$time = '<'.'time'.'>'.gmdate("H:i:s").'</time'.'>';

$file_contents = preg_replace( $p_date, $date, $file_contents );
$file_contents = preg_replace( $p_time, $time, $file_contents );

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

echo "Current date: ".$date."\n";
echo "Current time: ".$time."\n";

$bytes_written = file_put_contents( $cdir.'/'.$source_file_name.".xml", $file_contents );

// Make sure that the number of bytes written matches!
$ok = (strlen( $file_contents ) === $bytes_written );

echo "Update of ".$source_file_name." ";
$msg = $ok ? 'Success!':'Failure to write to target file!';

echo $msg;

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Part2: generate deps.$version.txt file

@require "PEAR/XMLParser.php";
if (!class_exists('PEAR_XMLParser'))
{
	echo 'This tools requires PEAR/XMLParser library.'."\n";
	die(0);
}

// extract version #
$first_dot = strpos( $source_file_name, '.' );

$version = substr(	$source_file_name, 
					$first_dot );
				
// the target file name is of the form
$target_file_name = 'deps'."{$version}".".txt";

echo 'Target file name: '.$target_file_name."\n";

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$parser = new PEAR_XMLParser;
$result = $parser->parse( $file_contents );

echo 'Parsing: '.($result ? 'OK':'Fail')."\n";
if (!$result)
	die(0);
	
#var_dump( $parser->getData() ); // debug

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$data = $parser->getData();
$deps = $data['dependencies'];
$s_deps = serialize( $deps );
echo 'Serialized dependencies: '.$s_deps."\n";

$bytes_written = file_put_contents( $cdir."/".$target_file_name, $s_deps );

// Make sure that the number of bytes written matches!
$ok = (strlen( $s_deps ) === $bytes_written );

$msg = $ok ? 'Success!':'Failure to write to target file!';

echo $msg;