<?php
/**
 * @author Jean-Lou Dupont
 * @package ConditionalContent
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>*/
if (class_exists( 'StubManager' ))
{
	$wgExtensionCredits['other'][] = array( 
		'name'        => 'ConditionalContent', 
		'version'     => '1.0.0',
		'author'      => 'Jean-Lou Dupont', 
		'description' => 'Provides a parser function for conditionally including content',
		'url' 		=> 'http://mediawiki.org/wiki/Extension:ConditionalContent',			
	);

	StubManager::createStub2(	
		array(	'class' 		=> 'ConditionalContent', 
				'classfilename'	=> dirname(__FILE__).'/ConditionalContent.body.php',
				'mgs'			=> array( 'cc', 'ccd' ),
				)
	);
}
else
	echo '[[Extension:ConditionalContent]] requires [[Extension:StubManager]] and optionally [[Extension:ParserPhase2]].';
//</source>