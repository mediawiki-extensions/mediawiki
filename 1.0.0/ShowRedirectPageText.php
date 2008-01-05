<?php
/**
 * @author Jean-Lou Dupont
 * @package ShowRedirectPageText
 * @version 1.0.0
 * @Id $Id: SecureHTML.body.php 782 2007-12-19 20:53:31Z jeanlou.dupont $
 */
//<source lang=php>*/
if (class_exists( 'StubManager' ))
{
	$wgExtensionCredits['other'][] = array( 
		'name'        => 'ShowRedirectPageText', 
		'version'     => '1.0.0',
		'author'      => 'Jean-Lou Dupont', 
		'description' => 'Provides viewing a wikitext included in a redirect page',
		'url' 		=> 'http://mediawiki.org/wiki/Extension:ShowRedirectPageText',			
	);
	
	StubManager::createStub(	'ShowRedirectPageText', 
								dirname(__FILE__).'/ShowRedirectPageText.php',
								null,
								array( 'ArticleViewHeader', 'OutputPageParserOutput' ),
								false,	// no need for logging support
								null,	// tags
								null,	// no parser functions
								null	// no magic words
							 );
}
else
	echo '[[Extension:ShowRedirectPageText]] requires [[Extension:StubManager]]';
//</source>