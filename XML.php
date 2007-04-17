<?php
/*
 * XML.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Facilitates client-side XML processing.
 *
 * COMPATIBILITY:  tested on MW 1.8.2 and 1.9.3
 *
 * Description:
 * -- Works in two phases:
 *    1) On 'normal' article view:
 *       a) Called through the parser tag hook <xml>
 *       b) Either calls the 'old hook' (e.g. GeSHi) OR
 *       c) Removes the <xml> tagged section from the content to view
 *       d) Inserts a JS script variables for client-side context
 *       e) By default, the <xml> content is *not* processed for viewing
 *          (this extension is mainly aimed at preparing a client-side
 *           AJAX process that would fetch the <xml> enclosed data section).
 *       f) The same workflow applies to <xsl> sections.
 *
 *    2) On 'ArticleEx' article view:
 *       a) Updates the mime type to 'text/xml'
 *       b) Resolves any MW: namespace references to
 *          local server URLs 
 *
 *       *** NOTE that the major lifting is done with "ArticleEx" class 
 *       extension for the 'phase 2' related workflow.    
 *
 * MISC:  SEE THE FILE 'XMLclass.php' for MANY additional details.
 * 
 * HISTORY:
 * -- Version 1.0
 * -- Version 1.1 - Added automatic insertion of <div id='xmltable'></div>
 * -- Version 1.2 -a) Added support for ' and " string delimiters in <mw:include> directive.
 *                -b) Added support for {{#xsl:src='page'}} magic word.
 *
 * -- Version 1.3 -a) Support for XML 'data islands'
 *                -b) Corrected bug related to non-existing 'include' article.
 *
 * -- Version 1.4 -a) Changed 'hInclude' to use 'str_replace' instead of slower 'str_ireplace'
 *                    (factor of 2 observed on an operation of ~800 substitutions performed).
 *                 b) small optimisation in 'hInclude'
 *
 * -- Version 1.5 -a) Added 'setparameter' to the {{#xsl:}} magic word for passing parameters to a stylesheet
 *                 b) Added support for 'server-side' processing.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'XML processor', 
	'version' => '1.5',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once("XMLclass.php");
$wgExtensionFunctions[] = "wfXMLclassSetup";
$wgHooks['LanguageGetMagic'][] = create_function('&$mg, &$langcode','$mg["xsl"] = array(0,"xsl"); return true;'); // v1.2b

// Global object collection
$xmlObj     = null;
$xmlOldHook = null;  // integration with GeSHi
$xslOldHook = null;

function wfXMLclassSetup()
{
	global $xmlObj;
	global $xmlOldHook;
	global $xslOldHook;
	
	$GLOBALS['xmlObj'] = XMLclass::singleton(); 
	
	// Phase 1 flow
	global $wgParser;
	$xmlOldHook = $wgParser->setHook( "xml", array( &$xmlObj, 'xml' ) );
	$xslOldHook = $wgParser->setHook( "xsl", array( &$xmlObj, 'xsl' ) );
	$wgParser->setFunctionHook( 'xsl', array( &$xmlObj, 'mg_xsl' ) );  // v1.2b
		
	// Keep old hooks in case we need them (i.e. GeSHi integration)
	$xmlObj->setXmlOldHook( $xmlOldHook );
	$xmlObj->setXslOldHook( $xslOldHook );
	
	// Phase 2 flow
	global $wgHooks;
	// From ArticleEx class
	$wgHooks['ArticleViewEx'][] = array( $xmlObj, 'hArticleViewEx' );
}
?>