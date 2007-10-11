<?php
/**
 * @author Jean-Lou Dupont
 * @package DirectoryManager
 * @version $Id$
 */
//<source lang=php>
DirectoryManager::$msg = array();

DirectoryManager::$msg['en'] = array(
'directorymanager'.'title'	=> 'Directory Manager',
'directorymanager'.'view'	=> 'View directory <i>$1</i>',
'directorymanager'.'-template'=> 
'	<filepattern>[[Filesystem:$1]]</filepattern>
	<dirpattern>{{#directory:$1|$1}}</dirpattern>
	<linepattern>$1<br/></linepattern>

	<b>Directory Listing</b>
<br/>
',
#'directorymanager'.''=> '',
);
//</source>