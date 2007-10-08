<?php
/**
 * @author Jean-Lou Dupont
 * @package InterWikiLinkManager
 */
//<source lang=php>*/
$wgExtensionCredits['other'][] = array( 
	'name'        => 'InterWikiLinkManager', 
	'version'     => StubManager::getRevisionId( '$Id$' ),
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Manages the InterWiki links table.',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:InterWikiLinkManager'		
);

StubManager::createStub(	'InterWikiLinkManager', 
							dirname(__FILE__).'/InterWikiLinkManager.body.php',
							null,
							array( 'ArticleSave', 'EditFormPreloadText' ),
							false,					// no need for logging support
							null,					// no tags
							array('iwl'),			// 1 parser functions
							null,					// no magic words
							array( NS_MEDIAWIKI )	// namespace triggering
						 );
//</source>
