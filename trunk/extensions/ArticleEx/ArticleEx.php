<?php
/*
 * ArticleEx.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 * Purpose:  Extends the MW Article class to handle
 *           the serving of pages without skin and addressed
 *           through explicit type information.
 *           Example:
 *           /index.php?title=Main:somexmldatapage.xml
 *           The actual MW stored article is titled:
 *           "Main:somexmldatapage" and the "raw" form
 *           is addressed with the above URL.
 *
 * Supported types:  all.  Data is retrieved from
 *                   a section delimited with an XML style
 *                   tag.  E.g. for an XSL XML stylesheet,
 *                   the page data would be enclosed in
 *                   <xsl> stylesheet data here </xsl>  
 *
 * Tested Compatibility: MW 1.8.2
 *
 * History:
 * v1.0		Initial availability
 * v1.1		- Added "ArticleViewEx" hook
 * v1.2		- Added "ArticleViewExBegin" hook
 *          - Added a general purpose "attributes" array.
 *          - Added the capability to flag is the said article
 *            is the first loaded in a transaction.
 * v1.3		- Corrected major bug: if article did not exists
 *			  would cause a fatal error.
 * v1.4		- Corrected maor bug: if an non-existing page from
 *            the following namespaces is requested (NS_MEDIA,
 *            NS_CATEGORY, NS_IMAGE), the standard processing
 *            flow should have been continued.
 * v1.5     - Corrected bug: initialised attributes array AFTER
 *            setting some variables causing loss of variables...
 * v1.6     - Added support for retrieving the 'categorylinks'
 *            when a valid article is loaded. The data can be found
 *            in '$this->categories'.
 *            This functionality is especially useful when templates
 *            require 'categorisation' level information.
 *          - Added initialisation of the global variable 'wgArticle'.
 *            Normally, this global variable is initialised a little bit
 *            too late to be useful i.e. after the action on the page is
 *            actually performed (see Wiki.php 'initialize' function).
 *
 * v1.7     - (a) Added chaining capability to 'ArticleViewExBegin' hook.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'ArticleEx',
	'version' => '$LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once(dirname( __FILE__ ) . '/ArticleExClass.php');

$wgExtensionFunctions[] = "wfArticleExSetup";

function wfArticleExSetup()
{
	global $wgHooks;
	$wgHooks['ArticleFromTitle'][] = 'wfArticleExInit';	
}

function wfArticleExInit( &$title, &$article )
{
	// What really counts is what is returned in $article.
	$GLOBALS['wgArticle'] = new ArticleExClass( $title, $article, true );
	return true;	
} 
?>