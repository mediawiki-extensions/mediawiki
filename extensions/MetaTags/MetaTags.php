<?php
/**
 * @author Jean-Lou Dupont
 * @package MetaTags
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
if (!class_exists('StubManager') || version_compare( StubManager::version(), '1.3.0', '<' ) )
	echo "<a href='http://mediawiki.org/wiki/Extension:MetaTags'/> <b>requires</b> <a href='http://mediawiki.org/wiki/Extension:StubManager'/> of version >= 1.3.0";
else
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'MetaTags',
		'version'		=> '@@package-version@@',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:MetaTags',	
		'description' 	=> "Provides META and LINK tags to HEAD whilst integrating with parser caching.", 
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'MetaTags', 
										'classfilename'	=> dirname(__FILE__).'/MetaTags.body.php',
										'mgs'			=> array( 'meta', 'link' )
									)
							);
							
	// only available since StubManager v1.3.0							
	StubManager::registerState( 'MetaTags',  StubManager::STATE_OK );							
}
//</source>
