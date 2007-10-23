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
		if ( $title->isLocal() )
			return true;
		
		$url = urldecode( $url );
			
		return true;
	}	
}
//</source>