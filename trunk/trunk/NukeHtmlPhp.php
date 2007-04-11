<?php
# NukeHtmlPhp Mediawiki Extension
# -------------------------------
# Author: Jean-Lou Dupont
 
# This extension implements a "hook" on the "ArticleSave" event
# in order to 'nuke' any <html>, <runphppage> or <runphp> tags
# found to be entered by members without the 'coding' right.

# To activate the extension, include it from your "LocalSettings.php"
# with: include("extensions/nukeHtmlPhp.php");

# Also, specify which group(s) have the 'coding' right e.g:
# $wgGroupPermissions['user']['coding'] = false;
# $wgGroupPermissions['sysop']['coding'] = true;
#
# HISTORY:
# v1.0
# ---------------------------------
$wgExtensionCredits['other'][] = array(
    'name'   => "NukeHtmlPhp [http://www.bluecortex.com]",
	'version'=> "v1.0",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

$wgHooks['ArticleSave'][] = 'fnNukeHtmlPhpHook';

function fnNukeHtmlPhpHook(&$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags)
{
 #First, check if User has "coding" permission
 if ($user->isAllowed('coding')!='true')
 {
  #If not, "nuke" all HTML, PHP and RUNPHP tags
  $text=str_ireplace("html>","ehtml>",$text);
  
  # turn tags to GeSHi compatible tags for highlighting
  $text=str_ireplace("<runphppage>","<php>{{",$text);
  $text=str_ireplace("</runphppage>","}}</php>",$text);
  
  $text=str_ireplace("<runphp>","<php>",$text);
  $text=str_ireplace("</runphp>","</php>",$text);
 }
  
 return true;
}
?>