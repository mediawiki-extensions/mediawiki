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
		
		// get rid of the query string because it messes up
		// most of the external web sites I am interested in.
		$query = '';
		
		return true;
	}	
}
//</source>