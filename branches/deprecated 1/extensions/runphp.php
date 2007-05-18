<?php
# runphp Mediawiki Extension
# CAUTION CAUTION CAUTION
# ONLY USE THIS IN AN TRUSTED ENVIRONMENT 
# CAUTION CAUTION CAUTION
# This extension allows you to run PHP-Code from Wiki-Articles using the following syntax:
# <runphp> phpcode; </runphp>
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/runphp.php");
# Author: Matthias Zirngibl - http://masterbootrecord.de

$wgExtensionFunctions[] = "wfRunPHP";

function wfRunPHP() 
{
    global $wgParser;

    $wgParser->setHook( "runphp", "parsePHP" );
}

function parsePHP( $input, $argv, &$parser ) 
{
    ob_start();
    eval($input);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
?>