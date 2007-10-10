<?php
/*
	Creates a 'blank' new /r/$dir with appropriate files
	@author: Jean-Lou Dupont
*/

$tpl = array(
	'1.0.0.xml',
	'package.1.0.0.xml',
	'stable.txt',
	'allreleases.xml',
);

if (!isset( $argv[1]))
{
	echo "Requires an existing directory!\n";	
	die(0);
}

// grab the source file from the command line
$source_dir_name = $argv[1];
$package_name    = basename( $source_dir_name );

if (!is_dir( $source_dir_name ))
{
	echo "Requires an existing directory!\n";	
	die(0);
}

$tdir = dirname( $source_dir_name );
$packageH = $package_name;
$packageL = strtolower( $package_name );

echo 'Assuming template directory: '.$tdir."\n";
echo 'Source directory:            '.$source_dir_name."\n";
echo 'Using package name:          '.$package_name."\n";
echo 'Lower case package name:     '.$packageL."\n";

$errors = 0;
foreach( $tpl as $template )
{
	// read the template
	echo "Fetching template ($template.tpl) ... ";
	$contents = @file_get_contents( $tdir.'/'.$template.'.tpl' );
	if (empty($contents )) 
		$errors ++;
	$msg = (!empty( $contents )) ? ' success':'failure';
	echo $msg."\n";
	$contents = str_replace('$packageH', $packageH, $contents );
	$contents = str_replace('$packageL', $packageL, $contents );	
	echo "Writing ($template) ... ";	
	$bytes_written = @file_put_contents( $source_dir_name.'/'.$template, $contents );
	if ( strlen( $contents) != $bytes_written )
		{ echo 'failure'; $errors ++; }
	else
		echo 'success';
	echo "\n";
}

echo "Completed with $errors errors.";