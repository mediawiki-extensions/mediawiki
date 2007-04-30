<?php
/*
 * @author Jean-Lou Dupont -- www.bluecortex.com
 * @package MediaWiki
 * @subpackage Extensions
 * 
 * <b>Purpose:</b>  This extension adds a 'read' restriction to protected article.
 * Only the users with the 'author' permission can 'read' the protected article.
 * The extension adds functionality to the existing 'protect' function of MW. 
 *
 * Installation:
 * include("extensions/AuthorRestriction.php");
 *
 * HISTORY:
 * V1.1   
 *  - Corrected shortcoming when dealing with non-'read' actions.
 * v1.2
 *  - Changed loading order of extension in order to integrate better
 *    with other Namespace Permission type extensions.
 * -- Moved to SVN management
 * v2.0 - Integration with ArticleEx to get rid of patch in Article.php
 *        (almost complete re-write)
 */

$wgExtensionFunctions[] = 'AuthorRestrictionSetup';
global $wgHooks;
$wgHooks['SpecialVersionExtensionTypes'][] = 'AuthorRestrictionSpecialPage' ;

function AuthorRestrictionSetup()
{
	global $wgMessageCache, $wgRestrictionTypes, $wgRestrictionLevels, $wgHooks ;

	$wgRestrictionTypes[] =      'read';
	$wgMessageCache->addMessage( 'restriction-read' ,    'Read' );
	$wgMessageCache->addMessage( 'protect-level-author', 'Authors Only' );
	$wgRestrictionLevels[] =     'author';
  
	global $wgHooks;
	$wgHooks['ArticleViewExBegin'][] = 'AuthorRestrictionUserCan';
    
	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array(
		'name'    => "AuthorRestriction",
		'version' => 'v1.3 $LastChangedRevision$',
		'author'  => 'Jean-Lou Dupont [http://www.bluecortex.com]',
		'description' => 'ArticleEx extension status: '
	);
}

function AuthorRestrictionUserCan( &$article )
{
  global $action, $wgUser;
  
  # if the action is not related to a 'view' (i.e. 'read') request, get out.
  if ($action != 'view')
   return true;  #don't stop processing the hook chain

  // Load any restriction associated with the 'read' right
  $r = $article->mTitle->getRestrictions('read');
    
  // If 'author' restriction is active, then check for 'author' right
  if ( in_array('author', $r) === true ) 
  {
    // Does the user belongs in the 'author' group?
  	#if ( !in_array('author', $user->getGroups()) )
  	if ( !in_array('author', $wgUser->getGroups()) )
	{
		global $wgOut;
		$wgOut->setPageTitle( wfMsg( 'badaccess' ) );
		$wgOut->addWikiText( wfMsg( 'badaccess-group0' ) );
		$wgOut->output();
		exit;
	}	
  }
  return true; # don't stop processing hook chain.
}
function AuthorRestrictionSpecialPage()
{
	global $wgExtensionCredits;

	if (class_exists('ArticleExClass'))
		$result = 'Found -- Author Restriction extension operational!';
	else
		$result = '<b>not found -- Author Restriction extension not operational!</b>';

	foreach ( $wgExtensionCredits['other'] as $index => &$el )
	{
		if ($el['name']=='AuthorRestriction')
			$el['description'].=$result;
	}
}

?>