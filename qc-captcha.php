<?php
# QuickCaptcha Mediawiki Extension
# -------------------------------
# ADAPTED BY: Jean-Lou Dupont

# ORIGINAL CODE FROM:
# QuickCaptcha 1.0 - A bot-thwarting text-in-image web tool.
# Copyright (c) 2006 Web 1 Marketing, Inc.
 
# This extension implements a "hook" on the "UserCreateForm" event
# in order to insert the "Quick Captcha" during the account creation process.

# To activate the extension, include it from your "LocalSettings.php"
# with: include("extensions/qc-catpcha.php");

# require_once("extensions/qc-settings.php");
# require_once("extensions/qc-imagebuilder.php");

$wgHooks['UserCreateForm'][] = 'fnQcCaptcha';

function fnQcCaptcha(&$template)
{
 $template->set('fnQcOutCaptcha',"<p><img src='/extensions/qc-imagebuilder.php' border='1'></p><p><label for='captcha'>Enter captcha code</label><br /><input type='text' name='captcha' id='captcha' size='8' maxlength='8' value='' /></p>");
}

function fnQcOutCaptcha()
{

}

?>