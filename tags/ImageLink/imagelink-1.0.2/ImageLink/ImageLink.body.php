<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 * @version $Id$
 */
//<source lang=php>*/

class ImageLink
{
	// constants.
	const thisName = 'ImageLink';
	const thisType = 'other';
	
	var $links;
	
	public function __construct() {}
	
	public function mg_imagelink( &$parser, $img, $page,  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null )// optional parameters
	/**
	 *  $img  = image reference i.e. a valid image name e.g. "New Clock.gif" 
	 *  $page = page reference i.e. a valid page name e.g. "Admin:Show Time"
	 *
	 * {{#imagelink:New Clock.gif|Admin:Show Time|alternate text}}
	 */
	{
		$ititle = Title::newFromText( $img );

		// this really shouldn't happen... not much we can do here.		
		if (!is_object($ititle)) 
			return;

		// check if we are dealing with an InterWiki link
		if ( $ititle->isLocal() )
		{
			$image = Image::newFromName( $img );
			if (!$image->exists()) 
				return '[[Image:'.$img.']]';
	
			$iURL = $image->getURL();
		}
		else
			$iURL = $ititle->getFullURL();
			
		if (empty($page)) 
			return 'ImageLink: missing page reference ';

		$ptitle = Title::newFromText( $page );
		
		// this might happen in templates...
		if (!is_object( $ptitle ))
			return 'ImageLink: invalid title name.';
				
		if ( $ptitle->isLocal() )
		{
			$tURL = $ptitle->getLocalUrl();
			$aClass=''; 			
		}
		else
		{
			$tURL = $ptitle->getFullURL();
			$aClass = 'class="extiw"';
		}		
		
		// Optional parameters
		if ($alt    !== null)	$alt    = "alt='${alt}'"; 		else $alt='';
		if ($width  !== null)	$width  = "width='${width}'"; 	else $width='';
		if ($height !== null)	$height = "height='${height}'";	else $height='';
		if ($border !== null)	$border = "border='${border}'";	else $border='';

		$t = "_imagelink_".date('Ymd').count($this->links)."_/imagelink_";
				
		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		$this->links[] = "<a ".$aClass." href='${tURL}'><img src='${iURL}' $alt $width $height $border /></a>";

		return $t;
	}

	/**
	 	This function is called just before the HTML is rendered to the client browser.
	 */
	public function hParserAfterTidy( &$parser, &$text )
	{
		// Some substitution to do?
		if (empty($this->links)) return true;

		foreach($this->links as $index => $link)
		{
			$p = "/_imagelink_".date('Ymd').$index."_\/imagelink_/si";
			$text = preg_replace( $p, $link, $text );
		}
	
		return true;
	}
} // end class definition.
//</source>