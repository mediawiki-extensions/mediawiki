<?php
/**
 * @author Jean-Lou Dupont
 * @package AlternateRawPage
 * @version @@package-version@@
 * @Id $Id$
*/
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'    	=> 'AlternateRawPage',
	'version' 	=> '@@package-version@@',
	'author'  	=> 'Jean-Lou Dupont',
	'description' => "Provides an alternative way of retrieving pages in 'raw' format", 
	'url' 		=> 'http://mediawiki.org/wiki/Extension:AlternateRawPage',	
);

$wgHooks['ArticleFromTitle'][] = 'AlternateRawPage_ArticleFromTitle';

function AlternateRawPage_ArticleFromTitle( &$title, &$article )
{
	// let mediawiki handle those.
	$ns = $title->getNamespace();
	if (NS_MEDIA==$ns || NS_CATEGORY==$ns || NS_IMAGE==$ns)
		return true;

	$titre = $title->getText();
	
	$bits = explode( '/', $titre );	

	$last_pos = count( $bits )-1;	
	
	// if the title does not end with '/raw', then bail out
	if ( $bits[ $last_pos ] !== 'raw' )
		return true;

	// From this point, we know we have a request to send the
	// article in 'raw' format. Let's play some tricks on
	// MediaWiki now...
	unset( $bits[ $last_pos ] );
	
	// Construct new title & article objects
	$new_title_text = implode( '/', $bits );
	$title = Title::newFromText( $new_title_text );
	$article = new Article( $title );

	// ... and let's change the request ;-)
	global $mediaWiki;
	
	// need to send the parameter by reference...
	$by_ref = 'raw';
	
	$mediaWiki->setVal( "action", $by_ref );
	
	// play nice.
	return true;
}
					
//</source>