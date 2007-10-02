<?php
/*
	PEAR Channel Update Tool: update 'categories.xml' file
	Command Line Utility
	@author: Jean-Lou Dupont
	$Id$
	
	Execute from within the SVN repository in the REST/c directory.
*/
//<source lang=php>

require (realpath(dirname(__FILE__).'/../../parse.channel.php'));

if (empty(Channel::$uri))
{
	echo 'Unable to derive URI of channel using channel.xml file!'."\n";	
	var_dump( Channel::$data );
	die(0);
}

echo 'Channel uri='.Channel::$uri."\n";

$template_file_name = 'categories.xml.tpl';
$template_line 		= '<c xlink:href="/rest/c/$category/info.xml">$category</c>'."\n";
$target_file_name	= 'categories.xml';
$replacement 		= '$category';
$replacementUri 	= '$uri';

// ------------------------------
$cdir = dirname( __FILE__ );
$tplFN = $cdir.'/'.$template_file_name;
$target = $cdir.'/'.$target_file_name;
// ------------------------------

// Get template file contents
$tpl = file_get_contents( $tplFN );

// Now read all the directories which effectively
// constitutes the list of categories
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

// go through the director list to produce
// the replacement string for $contents
$contents = null;
if (empty( $dirs ))
{
	echo "Nothing to do!\n";	
	die(1);
}

foreach( $dirs as $dir )
{
	$c = str_replace( $replacement, $dir, $template_line );
	$contents .= $c;
}

#echo $contents; // debug

// now, replace $contents in the template file
$new_contents = str_replace('$contents', $contents, $tpl );

$final_contents = str_replace($replacementUri, Channel::$uri, $new_contents );

// Finally, write the template in the target file
$path = $cdir.'/'.$target_file_name;
$bytesWritten = file_put_contents( $path, $final_contents );

$ok = (strlen($final_contents) === $bytesWritten);
$msg = $ok ? "Success!":"Failure to write target file!";

echo $msg;
//</source>