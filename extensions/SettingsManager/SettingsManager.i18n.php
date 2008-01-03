<?php
/**
 * @author Jean-Lou Dupont
 * @package SettingsManager
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>

global $logSettingsManager;
$logSettingsManager = 'mngns';	

global $msgSettingsManager;
$msgSettingsManager['en'] = array(
// en section

	// LOG related
'mngs' 						=> 'Settings Manager',
'mngs'.'logpage' 			=> 'Settings Manager Log',
'mngs'.'logpagetext'		=> 'This is a log of changes related to the managed namespaces',
'mngs'.'logentry'			=> '',
'mngs'.'-updtok-entry'		=> "Update OK",
'mngs'.'-updtfail1-entry'	=> "Update Failure",
'mngs'.'-updtfail2-entry'	=> "Update Failure",
'mngs'.'-updtfail3-entry'	=> "Update OK",
'mngs'.'-updtfail4-entry'	=> "Update Failure",

'mngs'.'-updtok-text'		=> "The namespace definition file was successfully updated.",
'mngs'.'-updtfail1-text'	=> "The template file couldn't be loaded.",
'mngs'.'-updtfail2-text'	=> "The namespace definition file couldn't be written.",
'mngs'.'-updtfail3-text'	=> "Nothing to update.",
'mngs'.'-updtfail4-text'	=> "Namespace file not writable",

'settingsmanager'.'-incorrect-page' 	=> 'The parser function <b>#mns</b> can not be used on this page.<br/>',
'settingsmanager'.'-insufficient-right'=> 'Insufficient right to execute the parser function <b>#mns</b>.<br/>',

'settingsmanager'.'-invalid-index'		=> 'Invalid namespace index <b>$1</b> (immutable)',
'settingsmanager'.'-invalid-name'		=> 'Invalid namespace name <b>$1</b> (immutable)',
'settingsmanager'.'-invalid-identifier'=> 'Invalid namespace identifier <b>$1</b> (immutable)',

'settingsmanager'.'-invalid-index-2'	=> 'Invalid namespace index <b>$1</b> (already defined)',
'settingsmanager'.'-invalid-name-2'	=> 'Invalid namespace name <b>$1</b> (already defined)',
'settingsmanager'.'-invalid-identifier-2'=> 'Invalid namespace identifier <b>$1</b> (already defined)',

'settingsmanager'.'-open-code'			=> '$bwManagedNamespaces = array('."\n",
'settingsmanager'.'-entry-code'		=> "'".'$1'."' => '".'$2'."',\n",
'settingsmanager'.'-close-code'		=> ');'."\n",

'settingsmanager'.'-open-code2'		=> '$bwManagedNamespacesDefines = array('."\n",
'settingsmanager'.'-entry-code2'		=> "'".'$1'."' => '".'$2'."',\n",
'settingsmanager'.'-close-code2'		=> ');'."\n",

#'managenamespaces'.'' => '',
#'' => '',
);
//</source>