<?php
/*
 * getJsAction.php
 *
 * Mediawiki extension.
 * @author: Jean-Lou Dupont
 *
 * Purpose: Extension used to return Javascript web objects
 *          stored in the Mediawiki page database.
 *
 * Usage:   Place your JS code in between 
 *          <javascript> code </javascript> tags and store
 *          the resulting page.
 *
 *
 * Dependancies:
 *  - RunPHP Class
 *  - UserClass (OPTIONAL)
 *  - HierarchicalNamespacePermissions (OPTIONAL, dependancy from UserClass)
 *  - GeSHi syntax highlighter (OPTIONAL, to have a user-friendly way of
 *                              looking at the JS code in MW).
 * 
 * HISTORY: v1.0  initial availability
 *
*/
$wgExtensionCredits['other'][] = array(
    'name'   => "getJs [http://www.bluecortex.com]",
	'version'=> "1.0",
	'author' => 'Jean-Lou Dupont' 
);

$wgHooks['UnknownAction'][] = 'getJsHandler' ;

function getJsHandler( $action, $article )
{
   // first, make sure we are asked to deal
   // with "getjs" action.
   if ( $action != "getjs" )
     return true;
	 	
   global $wgOut;
   global $wgInputEncoding;
   global $wgJsMimeType;
   global $wgUser;
   global $wgSquidMaxage;

   $mTitle =& $article->mTitle;

   // First, make sure the user is allowed this request.
   $ns    = $mTitle->getNamespace();
   $t     = $mTitle->mDbkeyform;
   $title = Namespace::getCanonicalName($ns).":".$t;

   // go through silently if
   // HierarchicalNamespacePermissions extension
   // not found.
   if (class_exists('UserClass'))
   {
	   if (! $wgUser->isAllowedEx( $ns, $title, "getjs") )		
	   {
	     wfHttpError( 403, 'Forbidden',
				'Unsufficient access rights.' );
	     return;
	   }
    }
  
  // But we need this following class to process	
  if (! class_exists('runphpClass') )
  {
     wfHttpError( 500, 'Internal Error',
			'Missing RunPHP Class component.' );
     return;
  }

  $rev = Revision::newFromTitle( $mTitle );
  if ( $rev ) 
  {
    $lastmod = wfTimestamp( TS_RFC2822, $rev->getTimestamp() );
    header( "Last-modified: $lastmod" );
  }
  $runphp = new runphpClass;
  $runphp->init( $title );
 
  $code = $runphp->getJsCode();

  // We have found a valid page, let's start building the response.
  header( "Content-type: ".$wgJsMimeType.'; charset='.$wgInputEncoding );

  // Output may contain user-specific data; vary for open sessions
  $mPrivateCache = ( $wgSquidMaxage == 0 ) ||
			( isset( $_COOKIE[ini_get( 'session.name' )] ) ||
			$wgUser->isLoggedIn() );
  header( 'Cache-Control: '.$mode.', s-maxage='.$wgSquidMaxage.', max-age='.$wgSquidMaxage );
  
  echo $code;
  $wgOut->disable();

  return false; # means stop looking for more.
  // END
}
?>
