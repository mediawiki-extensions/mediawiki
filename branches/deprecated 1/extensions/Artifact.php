<?php
/*
 * Artifact.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
*/
$wgExtensionCredits['other'][] = array( 
	'name'    => 'Artifact',
	'version' => '1.0', 
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

require_once("ArtifactClass.php");

$wgHooks['LanguageGetMagic'][] = 'wfArtifactClassSetupMagic';
$wgExtensionFunctions[] = "wfArtifactClassSetup";

$artifactObj = null;
function wfArtifactClassSetup()
{
	global $artifactObj;
	$GLOBALS['artifactObj'] = new ArtifactClass( );
	global $wgParser;
	$artifactObj->init( &$wgParser);
}

function wfArtifactClassSetupMagic( &$magicWords, $langCode )
{
	$magicWords['artifactlist'] = array( 0, 'artifactlist' );
	return true;
}

?>