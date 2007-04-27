<?php
# AfterEditRedirect.php 
# $LastChangedRevision$
#
# Mediawiki Extension
# -------------------------------
# Author: Jean-Lou Dupont
# 
# This extension is used to enhance the form processing capabilities
# of Mediawiki by offering the support for web page redirection after
# an "edit/save" operation.
#
# The forms are based on the standard 'editform' used by MediaWiki
# to make edits to articles. The standard form includes the 
# following HTML elements (non-exhaustive list):
# 1) wpTextbox1 textarea: where the edits take place
# 2) wpStarttime
# 3) wpEdittime
# 4) wpSection
# 5) wpSummary
# 6) wpMinoredit
# 7) wpWatchthis
# 8) wpSave | wpPreview | wpDiff
# 9) wpEditToken
# 
# The extension processes the non-native "wpRedirect" element.
#
# USAGE:
# ======
# Embed a "wpRedirect" element in a posted form using the following
# syntax:
# <input type='hidden' value='page' name='wpRedirect' />
#  where 'page' is a valid Mediawiki page in your project. 
#  E.g. "Main:Welcome Page" 
# 
# INSTALLATION:
# ============= 
# To activate the extension, include it from your "LocalSettings.php"
# with: include("extensions/AfterEditRedirect.php");
#
# DEPENDANCY:
# ===========
# This extension requires "AfterEdit" extension to operate.
#
# COMPATIBILITY:
# ==============
#
# Tested on Mediawiki v1.8.2
#
# HISTORY:
# v1.0    Initial availability
# ---------------------------------

$aeRedirect = "(v1.0)";

$wgExtensionCredits['other'][] = array(
    'name' => "AfterEditRedirect $aeRedirect [http://www.bluecortex.com]",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

$wgHooks['AfterEdit'][]   = 'hAfterEditRedirect';

function hAfterEditRedirect( &$editpage )
{
	global $wgRequest, $wgOut;
	
  	$ftRedirect = $wgRequest->getText('wpRedirect');
	
	if ($ftRedirect!='')
	{
		$t=Title::newFromText($ftRedirect);
		$wgOut->redirect( $t->getFullURL("") );	
	}
}

?>