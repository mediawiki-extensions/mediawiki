<?php
/**
 * @author Jean-Lou Dupont
 * @package InterWikiTitleWizard
 * @version $Id$ 
 */
//<source lang=php>
class InterWikiTitleWizard
{
	public function __construct() {}
	
	public function hGetFullURL( &$title, &$url, $query )
	{
		// we are only interested by inter-wiki titles
		if ( !$title->isExternal() )
			return true;
		
		$url = urldecode( $url );
		
		// get rid of the 'rdfrom' query string because it messes up
		// most of the external web sites I am interested in.
		$rdfrom = strpos($url, '?rdfrom');
		if ($rdfrom === false)
			return true;
			
		// chop it off!
		$url = substr( $url, 0, $rdfrom );
		
		return true;
	}	
}
//</source>