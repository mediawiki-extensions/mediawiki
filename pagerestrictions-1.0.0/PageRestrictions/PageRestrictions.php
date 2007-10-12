<?php
/**
 * @author Jean-Lou Dupont
 * @package PageRestrictions
 * @version $Id$
*/
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'		=> 'PageRestrictions',
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Adds page level restrictions definitions & enforcement.",
);

StubManager::createStub(	'PageRestrictions', 
							dirname(__FILE__).'/PageRestrictions.body.php',
							dirname(__FILE__).'/PageRestrictions.i18n.php',
							array('ArticleViewHeader'),					// hooks
							false, 					// no need for logging support
							null,					// tags
							null,
							null
						 );
//</source>
