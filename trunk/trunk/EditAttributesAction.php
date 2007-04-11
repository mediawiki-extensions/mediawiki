<?php
/*
 * EditAttributesAction
 * Mediawiki Extension
 *
 * @author: Jean-Lou Dupont (www.bluecortex.com)
 *
 * COMPATIBILITY:
 * - tested on Mediawiki v1.8.2
 *
 * Extension that provides the capability to execute PHP code on a
 * stored Mediawiki page upon the request "action=editattributes".
 *
 * This extension adds a new access right : 'editattributes'
 *
 * INSTALLATION:
 * =============
 * 1) Add the 'readattributes' right to your rights management system in place
 *   (either standard Mediawiki or e.g. NamespacePermissions2)
 *
 * 2) Add the necessary configuration for you 'edit action handler' through
 *    the 'UnknownActionHandler' extension.
 *    E.g.
 *    $uaObj->setHandler("editattributes","Admin:EditAttributes"); 
 * 
 * IMPORTANT USAGE NOTE:
 * =====================
 *
 * This extension relies heavily on companion extensions to operate.
 * Please consult the documentation on these extensions before
 * venturing using this one.
 * 
 * DEPENDANCY:
 *  Requires the extensions 
 *  1) 'Runphp_page'          [dependancy related to #2]
 *  2) 'UnknownActionHandler'
 *  3) PageAttributes
 *  4) NamespacePermissions2
 *
 * HISTORY:
 * V1.0    Initial availability.
 * V1.1    Added more fine grained rights (i.e. edit & read)
 * V1.2    Made the action in the sidebar always visible provide
 *         the user has sufficient access rights. 
 *
 */
$wgExtensionCredits['other'][] = array(
    'name'   => "EditAttributesAction [http://www.bluecortex.com]",
	'version'=> "1.2",
	'author' => 'Jean-Lou Dupont' 
);

$eaObj = new EditAttributesAction;
$wgHooks['SkinTemplateContentActions'][] = array( $eaObj, 'hEditAttributesActionHandler' );

$wgExtensionFunctions[] = 'EditAttributesActionSetup';

function EditAttributesActionSetup()
{
	global $wgMessageCache;
	
	$wgMessageCache->addMessage( 'readattributes' , 'Attributes' );  
}

class EditAttributesAction
{
	function EditAttributesAction() { }
	
	function hEditAttributesActionHandler( &$content_actions )
	{
		# only the global title is processed. 
		global $wgTitle, $wgUser;
		
		$id = $wgTitle->getArticleID();  #shortcut
		
		# first verify that this page contains attributes
		global $paObj;  # extension "PageAttributes" global object.
		
		if (!isset( $paObj ))
			return true;
		
		# check if the user is allowed to perform this action
		# The user must at least have 'read' permission to get to here!
		# extension "NamespacePermission2" can apply here OR
		# extension "Hierarchical Namespace Permissions"
		$r = $wgTitle->userCan("readattributes"); 

		if ($r)			
			$content_actions['readattr'] = array(
				'class' => ($r==true) ? 'selected':false,
				'text'  => wfMsg('readattributes'),
				'href'  => $wgTitle->getLocalURL( "action=readattributes" ) 
				);
		
		return true; # must return true or else other extensions will be cut dry	
	} #end handler
	
} #end class
?>