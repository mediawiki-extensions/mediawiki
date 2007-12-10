<?php
/**
 * @author Jean-Lou Dupont
 * @package PageToFile
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
// Format for global variables is:
// 'var-type'.$classname
//  where 'var-type' supported are:
//   'log', 'msg'
//
// Everything is anchored on the classname.

global $msgPageToFile;		// required for StubManager
global $logPageToFile;		// required for StubManager

// required for StubManager. The format is important:  'log'.$classname
$logPageToFile = 'page2file';	

// the format is important here too: 'msg'.$classname
$msgPageToFile['en'] = array(
	'page2file'							=> 'PageToFile log',
	'page2file'.'logpage'               => 'PageToFile log',
	'page2file'.'logpagetext'           => 'This is a log of <i>page to file commit</i> actions',
	'page2file'.'logentry'              => '',
	'page2file'.'-commitok-entry'    	=> 'File successfully committed',
	'page2file'.'-commitfail-entry'  	=> 'File commit failed',
	'page2file'.'-commitfail2-entry' 	=> 'File commit failed - invalid filename',	
	'page2file'.'-commit-text'       	=> "$1",
	#'' => '',
);
//</source>