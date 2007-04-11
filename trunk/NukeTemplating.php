<?php
# NukeTemplating Mediawiki Extension
# -------------------------------
# Author: Jean-Lou Dupont
# This extension implements a "hook" on the "ArticleSave" event
# in order to 'nuke' any template calls 
# found to be entered by members without the 'templating' right.
#
# This extension integrates with the <protect> extension i.e.
# the template calls within protected sections are left untouched.

# To activate the extension, include it from your "LocalSettings.php"
# with: include("extensions/NukeTemplating.php");

# Also, specify which group(s) have the 'coding' right e.g:
# $wgGroupPermissions['user']['templating'] = false;
# $wgGroupPermissions['sysop']['templating'] = true;
#
# HISTORY
# v1.0
# ---------------------------------
$wgExtensionCredits['other'][] = array(
    'name'   => "NukeTemplating [http://www.bluecortex.com]",
	'version'=> "v1.0",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

$wgHooks['ArticleSave'][] = 'fnNukeTemplatingHook';

function fnNukeTemplatingHook(&$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags)
{
	#First, check if User has "templating" permission
	if ($user->isAllowed('templating')!='true')
	{
		$r=preg_match("/<protect>(.*?)<\/protect>/si",$text);
		if ($r>0)
		{
			# FIXME
			# Only one <protect> section is processed at the moment.
			$pa="/\A(.*?){{(.*?)}}(.*?)<protect>/si";
			$pc="/<\/protect>(.*?){{(.*?)}}(.*?)\z/si";
	
			$text=preg_replace($pa,'$1(($2))$3<protect>',$text);
			$text=preg_replace($pc,'</protect>$1(($2))$3',$text);
		}
		else
		{
			$text=str_ireplace("{{","((",$text);
			$text=str_ireplace("}}","))",$text);
		}
	}
  
	return true;
}
?>