<?php
/*
 * ArticleCache.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides the following functions:
 *           1- article content caching
 *           2- recursive (bottom-up) article search
 *           3- article content getting (simple interface)
 * 
 *           This 'extension' is mainly meant to provide
 *           services to 'real' Mediawiki extensions.   
 * 
 * Tested Compatibility: MW 1.8.2, 1.9.3
 *
 * HISTORY:	v1.0
 *          v1.1 - added 'singleton' functionality.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'ArticleCache',
	'version' => '1.1',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once(dirname( __FILE__ ) . '/ArticleCacheClass.php');

// Global Object placeholder.
// There is probably not a compelling case for
// having multiple instances of this class.
$acGlobalObj = &ArticleCacheClass::singleton();
?>