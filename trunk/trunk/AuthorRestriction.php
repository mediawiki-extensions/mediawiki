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
 */
$wgExtensionCredits['other'][] = array(
    'name'    => "AuthorRestriction [http://www.bluecortex.com]",
	'version' => "1.2",
	'author'  => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);
$wgExtensionFunctions[] = 'AuthorRestrictionSetup';
$wgHooks['userCan'][] =      'AuthorRestrictionUserCan';

function AuthorRestrictionSetup()
{
  global $wgMessageCache, $wgRestrictionTypes, $wgRestrictionLevels, $wgHooks ;

  $wgRestrictionTypes[] =      'read';
  $wgMessageCache->addMessage( 'restriction-read' ,    'Read' );
  $wgMessageCache->addMessage( 'protect-level-author', 'Authors Only' );
  $wgRestrictionLevels[] =     'author';	
}

function AuthorRestrictionUserCan( $title, $user, $action, $result )
{
  # if the action is not related to a 'read' request, get out.
  if ($action != 'read')
  {
   $result=null; #result is indetermined from our point of view.
   return true;  #don't stop processing the hook chain
  }

  // Load any restriction associated with the 'read' right
  $r = $title->getRestrictions('read');
  $i = in_array('author', $r);
  
  // If 'author' restriction is active, then check for 'author' right
  if ( $i===true ) 
  {
    // Does the user belongs in the 'author' group?
  	if ( !in_array('author', $user->getGroups()) )
	  $result = false;
	else 
	  $result = true;
 
    #either case, stop processing the hook chain
	# because we encountered a 'read' restriction
	# and no other extension is assumed to handle these.
    return false; 
  }
  
  return true; # don't stop processing hook chain.
}
?>