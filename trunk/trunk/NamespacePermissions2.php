<?php
/* NamespacePermissions - MediaWiki extension
 * Version 2.0
 * 
 * provides separate permissions for each action (read,edit,create,move) 
 * on articles in custom namespaces for fine access management
 *
 * Author: Petr Andreev
 * Modified by: Jean-Lou Dupont
 *
 *
 * Sample usage:
 *
 * $wgExtraNamespaces = array(100 => "Foo", 101 => "Foo_Talk");
 * // optional (example): allow registered users to view and edit articles in Foo 
 * $wgGroupPermissions[ 'user' ][ 'ns100_read' ] = true;
 * $wgGroupPermissions[ 'user' ][ 'ns100_edit' ] = true;
 * // end of optional
 * require('extensions/NamespacePermissions.php');
 * 
 * Permissions provided:
 *   # ns{$num}_read
 *   # ns{$num}_edit
 *   # ns{$num}_create
 *   # ns{$num}_move
 * where {$num} - namespace number (e.g. ns100_read, ns101_create)
 *
 * Groups provided:
 *   # ns{$title}RW - full access to the namespace {$title}
 *   # ns{$title}RO - read-only access to the namespace {$title}
 *   e.g. nsFoo_talkRW, nsFooRO
 *
 * Version 2.0:
 *
 * - Creation of reference groups by both Number & Canonical Name
 * - Meant to serve as "last bastion" i.e. other extensions should be
 *   loaded before this one in LocalSettings.php
 *
 * Version 2.1:
 *
 * - Added ability to easily perform 'security audits'.
 *
 */
$nsPermissions2 = "(v2.1)";

$wgExtensionCredits['other'][] = array(
    'name' => "NamespacePermissions2 $nsPermissions2 [http://www.bluecortex.com]",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

// permissions for autocreated groups should be set now,
// before the User object for current user is instantiated
$wgExtensions[] = namespacePermissionsCreateGroups();

// other stuff should better be done via standard mechanism of running extensions
$wgExtensionFunctions[] = "wfNamespacePermissions";

// create groups for each custom namespace
function namespacePermissionsCreateGroups() 
{
    global $wgGroupPermissions, $wgExtraNamespaces;

    foreach ( $wgExtraNamespaces as $num => $title ) 
	{
# <JLD>
# Include the two forms of refence i.e. by number or by canonical name
/*
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$title}_edit" ]   = true;
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$title}_read" ]   = true;
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$title}_create" ] = true;
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$title}_move" ]   = true;
        $wgGroupPermissions[ "ns{$title}RO" ][ "ns{$title}_read" ]   = true;

        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$num}_edit" ]   = true;
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$num}_read" ]   = true;
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$num}_create" ] = true;
        $wgGroupPermissions[ "ns{$title}RW" ][ "ns{$num}_move" ]   = true;
        $wgGroupPermissions[ "ns{$title}RO" ][ "ns{$num}_read" ]   = true;
*/		
    }
	
}

function wfNamespacePermissions() 
{
    global $wgHooks;

    // use the userCan hook to check permissions
    $wgHooks[ 'userCan' ][] = 'namespacePermissionsCheckNamespace';
}

function namespacePermissionsCheckNamespace( $title, $user, $action, $result ) 
{
  # load namespace
  $ns = $title->getNamespace();

  $result = namespacePermissionsUserCan( $ns, $user, $action );
  
  # last bastion --> stop allowing more processing.
  return false;
}

/*
 * Use the following function to perform
 * security audits.
 * This function can be used with crafted 'User' and 'Namespace' parameters.
 *
 *
*/

function namespacePermissionsUserCan( $ns, $user, $action )
{
  # If explicitly allowed
  if ( $user->isAllowed("ns{$ns}_{$action}") === true ) 
    return true;

  # Else, disallow operation
  return false;
}

?>