<?php

/**
 * ConditionalTemplate
 * @package MediaWiki
 * @subpackage Extensions
 * @author Jean-Lou Dupont - http://bluecortex.com
 *
 * This extension enables the conditional transclusion of an article.
 * The syntax is the following:
 * {{#template:page | condition}}
 * where 'page' is the desired MW article
 * and 'condition' === true  --> page is transcluded
 *     'condition' === false --> page is not transcluded
 *
 * HISTORY:
 * v1.0
 */
$wgExtensionCredits['parserhook'][] = array(
    'name' => "ConditionalTemplate [http://www.bluecortex.com]",
	'version' => '$LastChangedRevision$',
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

$wgExtensionFunctions[]			= 'efCondTemplateSetup';
$wgHooks['LanguageGetMagic'][]	= 'efCondTemplateGetMagic';

function efCondTemplateGetMagic( &$magicWords, $langCode ) 
{
	$magicWords['template'] = array( 0, 'template' );
	return true;
}

function efCondTemplateSetup()
{
	global $wgParser;
	$wgParser->setFunctionHook( 'template', 'efCondTemplateExec' );	
}

function efCondTemplateExec(&$parser, $page, $cond = false )
{
	return ($cond ? CondTemplateLoadPage($page) : '' );
}

#
# LoadPage function
# 
function CondTemplateLoadPage( $p )
{
	global $mediaWiki;
	
	$title = Title::newFromText( $p );
	$article = $mediaWiki->articleFromTitle( $title );
	$article->loadContent();
	
	return $article->mContent;
}
?>