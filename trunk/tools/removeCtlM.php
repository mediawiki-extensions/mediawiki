<?php
if (!is_file($argv[1]))
{
	echo "Need valid input file!\n";	
	die(0);
}
$contents = file_get_contents( $argv[1] );

$contents = str_replace("\x00d",'', $contents );
$bytes_written = file_put_contents( $argv[1], $contents );

if (strlen($contents) != $bytes_written )
	echo "error writing file!\n";
	
echo "Completed!\n";