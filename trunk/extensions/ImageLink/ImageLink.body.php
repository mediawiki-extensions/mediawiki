<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 */
//<source lang=php>*/

$wgExtensionCredits[ImageLink::thisType][] = array( 
	'name'        	=> ImageLink::thisName, 
	'version'     	=> '1.0.0',
	'author'      	=> 'Jean-Lou Dupont', 
	'description' 	=> 'Provides a clickable image link',
	'url' 			=> 'http://mediawiki.org/wiki/Extension:ImageLink',			
);

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
		$title = Title::newFromText( $img );

		// this really shouldn't happen... not much we can do here.		
		if (!is_object($title)) return;

		// check if we are dealing with an InterWiki link
		if ( $title->isLocal() )
		{
			$image = Image::newFromName( $img );
			if (!$image->exists()) 
				return '[[Image:'.$img.']]';
			
			if (empty($page)) 
				return 'ImageLink: missing page reference ';
		
			$iURL = $image->getURL();

			$tURL = $title->getLocalUrl();
			$aClass=''; 			
		}
		else
		{
			$tURL = $title->getFullURL();
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