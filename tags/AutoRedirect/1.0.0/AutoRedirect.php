<?php
/**
 * @author Jean-Lou Dupont
 * @package AutoRedirect
 * @version 1.0.0
 * @Id $Id$
 */
if (class_exist('StubManager'))
{
	$wgExtensionCredits[AutoRedirect::thisType][] = array( 
		'name'    => 'AutoRedirect',
		'version' => '1.0.0',
		'author'  => 'Jean-Lou Dupont',
		'description' => "Provides a magic word to automatically create redirect pages", 
		'url'		=> 'http://mediawiki.org/wiki/Extension:AutoRedirect',
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'AutoRedirect', 
										'classfilename'	=> dirname(__FILE__).'/AutoRedirect.body.php',
										'mgs' 			=> array( 'autoredirect' ),
									)
							);
}
else
	echo 'Extension:AutoRedirect requires Extension:StubManager';						
//</source>
