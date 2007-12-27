<?php
/**
 * @author Jean-Lou Dupont
 * @package ManageNamespaces
 * @version 1.0.2
 * @Id $Id: ManageNamespaces.php 794 2007-12-27 00:47:16Z jeanlou.dupont $
 */
// <source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'ManageNamespaces',
		'version'		=> '1.0.2',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:ManageNamespaces',	
		'description' 	=> "Provides a special page to add/remove namespaces. "
	);
	
	StubManager::createStub2(	array(	'class' 		=> 'ManageNamespaces', 
										'classfilename'	=> dirname(__FILE__).'/ManageNamespaces.body.php',
										'i18nfilename'	=> dirname(__FILE__).'/ManageNamespaces.i18n.php',
										'logging'		=> true, 
										'hooks'			=> array( 'ParserAfterTidy' ),
										'nss'			=> array( NS_MEDIAWIKI ),
										'mgs'			=> array( 'mns' )
									)
							);
	
}
else
	echo "Extension:ManageNamespaces <b>requires</b> Extension:StubManager\n";

global $wgCanonicalNamespaceNames;
global $wgExtraNamespaces;
global $wgNamespacesWithSubpages;
global $bwManagedNamespaces;
global $bwManagedNamespacesDefines;
	
// Now include the managed namespaces in question
include( dirname(__FILE__).'/ManageNamespaces.namespaces.php' );

// Is the Namespace class defined yet?
if (!class_exists('Namespace') && !empty( $bwManagedNamespaces ))
	require($IP.'/includes/Namespace.php');

// Go through all the managed namespaces
if (!empty( $bwManagedNamespaces ))
	foreach( $bwManagedNamespaces as $index => $name )
	{
		// add the managed namespaces to the primary tables
		$wgCanonicalNamespaceNames[$index] = $name;
		$wgExtraNamespaces[$index] = $name;
				
		// Add subpage support for each of the managed namespaces		
		$wgNamespacesWithSubpages[ $name ] = true;
	}
// e.g. 	define('NS_BIZZWIKI',   100);
if (!empty($bwManagedNamespacesDefines))
	foreach($bwManagedNamespacesDefines as $index => $identifier )
		define("'$identifier'", $index );
		
//</source>