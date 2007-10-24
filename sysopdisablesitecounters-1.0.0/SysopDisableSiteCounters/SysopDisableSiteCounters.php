<?php
/**
 * @author Jean-Lou Dupont
 * @package SysopDisableSiteCounters
 * @version $Id$ 
 */
// <source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'SysopDisableSiteCounters',
	'version'		=> '1.0.0',
	'author'		=> 'Jean-Lou Dupont',
	'url'			=> 'http://www.mediawiki.org/wiki/Extension:SysopDisableSiteCounters',	
	'description' 	=> "Provides disabling the site statistic counters when pages are viewed by users of the sysop group.", 
);

global $wgExtensionFunctions;
$wgExtensionFunctions[] = create_function('',
'
	global $wgUser, $wgDisableCounters;
	if (in_array( "sysop", $wgUser->getEffectiveGroups() ))
		$wgDisableCounters = true;
	return;
');
//</source>