<?php
/**
 * Internationalisation file for ScriptsManager extension.
 *
 * @addtogroup Extensions
*/

$wgScriptsManagerLogMessages = array();

$wgScriptsManagerLogMessages['en'] = array(
	'editscriptlogpage'               => 'Edit Scripts log',                   # OK
	'editscriptlogpagetext'           => 'This is a log of scripts edited',    # OK
	'editscriptlogentry'              => '',                                   # For compatibility, don't translate this
	'editscriptlog-editsuccess-entry' => 'Script successfully edited',
	'editscriptlog-editfail-entry'    => 'Script edition failed',
	'editscriptlog-edit-text'         => "[[User talk:$1]]", # For compatibility <= MW 1.9, don't translate this
);
?>