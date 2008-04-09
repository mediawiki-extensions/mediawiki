<?php
/**
 * @author Jean-Lou Dupont
 * @package PageServer
 * @category ExtensionServices
 * @version @@package-version@@
 * @Id $Id: SmartyAdaptor.php 922 2008-01-15 19:41:09Z jeanlou.dupont $
 */
//<source lang=php>
if (!class_exists('StubManager') || version_compare( StubManager::version(), '1.3.0', '<' ) )
	echo "<a href='http://mediawiki.org/wiki/Extension:PageServer'/> <b>requires</b> <a href='http://mediawiki.org/wiki/Extension:StubManager'/> of version >= 1.3.0";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    	=> 'PageServer',
		'version' 	=> '@@package-version@@',
		'author'  	=> 'Jean-Lou Dupont',
		'description' => "Provides functionality to load & parse wiki pages stored in the filesystem.", 
		'url' 		=> 'http://mediawiki.org/wiki/Extension:PageServer',		
	);
		
	$wgAutoloadClasses['PageServer_Remote'] = dirname(__FILE__) . '/PageServer.remote.php';	
	
	StubManager::createStub2(	array(	'class' 		=> 'PageServer', 
										'classfilename'	=> dirname(__FILE__).'/PageServer.body.php',
										'mgs'			=> array( 'mwmsg', 'mwmsgx', 'load_page' ),
										'hooks'			=> array( 'page_server', 'page_remote' )										
									)
							);
	// Required PEAR class
	@include_once( "HTTP/Request.php" ); 

	// only available since StubManager v1.3.0
	$state = class_exists( 'HTTP_Request' ) ? StubManager::STATE_OK : StubManager::STATE_ATTENTION;
	StubManager::registerState( 'PageServer',  $state );
}
//</source>