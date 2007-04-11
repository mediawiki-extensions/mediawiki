<?php
# formtools.php Mediawiki Extension
# -------------------------------
# Author: Jean-Lou Dupont
# 
# This extension is used to process posted HTML forms to MediaWiki.
# The forms are based on the standard 'editform' used by MediaWiki
# to make edits to articles. The standard form includes the 
# following HTML elements (non-exhaustive list):
# 1) wpTextbox1 textarea: where the edits take place
# 2) wpStarttime
# 3) wpEdittime
# 4) wpSection
# 5) wpSummary
# 6) wpMinoredit
# 7) wpWatchthis
# 8) wpSave | wpPreview | wpDiff
# 9) wpEditToken
# 
# The user of this extension can customize the standard editform
# to support the creation of, amongst other things, comment forms.
# 
# To use the extension, one must embed the 'wpFormProc' HTML element
# in the posted form e.g.
# <input type='hidden' value='page' name='wpFormProc' />
# where 'page' refers to an existing MW page with the necessary
# PHP code to process the form. The PHP code can must be enclosed 
# in the <php> code here </php> tags.
# 
# To activate the extension, include it from your "LocalSettings.php"
# with: include("extensions/formtools.php");
# 
# NOTE: The extension requires also modification to "Article.php"
# in the "insertNewTitle" function to work.
# 'ArticleInsertComplete' hook comes too soon -- a redirect is
# applied in the code without any chance of running a "hook"
# to supercede it...
# 
# HISTORY:
# v1.0    Initial availability
# ---------------------------------

$formtoolsVersion = "(v1.0)";

$wgExtensionCredits['other'][] = array(
    'name' => "FormTools $formtoolsVersion [http://www.bluecortex.com]",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

$wgHooks['AlternateEdit'][]   = 'fnFormToolsAlternateEditHook';
$wgHooks['EditFilter'][]      = 'fnFormToolsEditFilterHook';

global $ftRedirect, $ftEditPage;

function fnFormToolsEditFilterHook(&$editPage,&$text,&$section,&$error)
{
	global $wgRequest, $wgParser, $mediaWiki, $ftEditPage;
	
	# check for a form processing command
	if ($wgRequest->getText('wpFormProc')!='')
	{
		# prepare a global object pointing to
		# the EditPage object passed.
		$ftEditPage=$editPage;
	
		# nothing gets out to the output buffer.
		ob_start();
	    $wgParser->disableCache();
		
	    $article=null;
	    $title=Title::newFromText($wgRequest->getText('wpFormProc'));
	    $article=$mediaWiki->articleFromTitle($title);
	    $article->loadContent();
	
		# the article's PHP code will have access
		# to the EditPage object through the global object.
		$page=$article->mContent;
		
		preg_match("/<php>(.*?)<\/php>/si",$page, $code);
	
	    eval($code[1]);
	
		# captured all the content from the 
		# output buffer. Processing of the form
		# should only result in content being
		# accumulated in the EditPage object.
	    $output = ob_get_contents();
	    ob_end_clean();
	}

	# say it's OK to carry on with default behavior
	return true;
}

# Required change to MW "Article.php" for the redirect function to work.
# Below is MW v1.8.2
# 
# 
#		$dbw =& wfGetDB( DB_MASTER );
#		if ($watchthis) {
#			if (!$this->mTitle->userIsWatching()) {
#				$dbw->begin();
#				$this->doWatch();
#				$dbw->commit();
#			}
#		} else {
#			if ( $this->mTitle->userIsWatching() ) {
#				$dbw->begin();
#				$this->doUnwatch();
#				$dbw->commit();
#			}
#		}
## <jld id="1">
## <file name="Article.php" />
## <func name="insertNewArticle" />
## helper for "formtools" extension: redirect function implementation.
#		global $ftRedirect, $wgOut;
#		if ($ftRedirect!='')
#		{
#			$t=Title::newFromText($ftRedirect);
#			$u=$t->getFullURL("");
#			$wgOut->redirect($u);	
#		}
#		else
# 
#			$this->doRedirect( $this->isRedirect( $text ) );
#	} # end of "insertNewArticle"
## </jld>

function fnFormToolsAlternateEditHook(&$editPage)
{
	global $ftRedirect, $wgRequest;

	# get a redirect command (if present)
  	$ftRedirect = $wgRequest->getText('wpRedirect');

	return true;	
}
?>