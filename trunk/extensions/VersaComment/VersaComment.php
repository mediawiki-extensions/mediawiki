<?php
/**
 * @author Jean-Lou Dupont
 * @package VersaComment
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>*/
if (class_exists( 'StubManager' ))
{
	$wgExtensionCredits['other'][] = array( 
		'name'        => 'VersaComment', 
		'version'     => '@@package-version@@',
		'author'      => 'Jean-Lou Dupont', 
		'description' => 'Provides versatile HTML comments',
		'url' 		=> 'http://mediawiki.org/wiki/Extension:VersaComment',			
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'VersaComment', 
										'classfilename'	=> dirname(__FILE__).'/VersaComment.body.php',
										'hooks'			=> array( 'ParserBeforeStrip' )
									)
							);
}
else
	echo '[[Extension:VersaComment]] requires [[Extension:StubManager]].';
//</source>