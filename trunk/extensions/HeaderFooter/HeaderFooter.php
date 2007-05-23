<?php
/*
 * HeaderFooter.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 * Purpose: insert an header and/or footer to an article
 *          upon a 'view' action.
 *
 * Features:
 * *********
 * - Recursive (bottom-up) search for "Header" and "Footer" subpages
 * - <noinclude> section support   
 * - Configurable per-namespace recursive level depth
 * - Configurable per-namespace enable/disable flag (all disable per default)
 *
 * DEPENDANCIES:
 * - ArticleCacheClass  (for recursive search function)
 *
 *
 * Tested Compatibility:  MW 1.8.2, 1.9.3
 *
 * HISTORY:
 * -- Version 1.1:	added support for <noheader/> and <nofooter/>
 * -- Version 1.11: changed interface to ArticleCacheClass: using singleton
 * -- Version 1.12: added check to ensure that the process is only performed once
 *                  i.e. the article requested in the transaction (and not other
 *                  articles fetched during the rendering process)
 *                  (helps with some other extensions that do fetch multiple pages
 *                   before returning the final render requested page).
 * -- Version 1.13: - Added support for '__NOHEADER__' and '__NOFOOTER__' magic words         
 * -- Version 1.14: - Added support for better parser cache integration
 *                    i.e. the normal MW behavior is to save an updated/created article
 *                    in the parser cache BEFORE this extension has the chance to execute.
 *                    This extension update disables this behavior but don't worry, the article will
 *                    be saved by MW the next time the article is viewed. 
 * -- Version 1.15: - Integration with 'ParserCacheControl' extension.
 * -- Version 1.16: - Added missing 'return true' in function handler.
 *                  - Added check for 'ParserCacheControl' in hook creation.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'HeaderFooter Extension', 
	'version' => 'v1.16 $LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once("HeaderFooterClass.php");
$hfObj = &HeaderFooterClass::singleton();
$wgHooks['ArticleAfterFetchContent'][] = array( $hfObj, 'hAddHeaderFooter' );

// v1.16
if (!class_exists('ParserCacheControl'))
	$wgHooks['ArticleSave'][] =          array( $hfObj, 'hArticleSave' );
?>