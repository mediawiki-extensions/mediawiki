<?php
//<source lang=php>
global $msgUserSettingsChangedLog;		// required for StubManager
global $logUserSettingsChangedLog;		// required for StubManager

// required for StubManager. The format is important:  'log'.$classname
$logUserSettingsChangedLog = 'usetchglog';	

// the format is important here too: 'msg'.$classname
$msgUserSettingsChangedLog['en'] = array(
	'usetchglog'					=> 'User Settings Changed Log',
	'usetchglog'.'logpage'			=> 'User Settings Changed Log',
	'usetchglog'.'logpagetext'		=> "This is a log of changes to a user's settings",
	'usetchglog'.'-saveok-entry'	=> 'Settings saved ok',
	'usetchglog'.'-save-text'		=> " user name [[User:$1]]",
	#'' => '',
);
