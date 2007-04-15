<?php
/*
 * HeaderFooter.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
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
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'HeaderFooter Extension', 
	'version' => '1.13',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once("HeaderFooterClass.php");
$hfObj = &HeaderFooterClass::singleton();
$wgHooks['ArticleAfterFetchContent'][] = array( $hfObj, 'hAddHeaderFooter' );
?>