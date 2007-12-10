<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>*/

class ImageLink
{
	// constants.
	const thisName = 'ImageLink';
	const thisType = 'other';
	
	var $links;
	
	public function __construct() {}
	
	public function mg_imagelink( &$parser, $img, $page='',  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null )// optional parameters
	/**
	 *  $img  = image reference i.e. a valid image name e.g. "New Clock.gif" 
	 *  $page = page reference i.e. a valid page name e.g. "Admin:Show Time"
	 *
	 * {{#imagelink:New Clock.gif|Admin:Show Time|alternate text}}
	 */
	{
		$html = $this->buildHTML( $img, $page, $alt, $width, $height, $border );
		
		$t = "_imagelink_".date('Ymd').count($this->links)."_/imagelink_";
				
		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		$this->links[] = $html;

		return $t;
	}
	/**
	 * Can be used with [[Extension:ParserPhase2]]
	 */
	public function mg_imagelink_raw( &$parser, $img, $page='',  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null )// optional parameters
	{
		return $this->buildHTML( $img, $page, $alt, $width, $height, $border );
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
	/**
	 * This method builds the HTML code relative to the required imagelink
	 */
	protected function buildHTML( $img, $page,  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null )
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
			return '';

		// prepare for 'link-less' case
		$anchor_open = '';
		$anchor_close = '';
		
		// check if we are asked to render a 'link-less' element
		if (!empty( $page ))
		{
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
			
			$anchor_open = "<a ".$aClass." href='${tURL}'>";
			$anchor_close = "</a>";
		}		
		// Optional parameters
		if ($alt    !== null)	$alt    = "alt='${alt}'"; 		else $alt='';
		if ($width  !== null)	$width  = "width='${width}'"; 	else $width='';
		if ($height !== null)	$height = "height='${height}'";	else $height='';
		if ($border !== null)	$border = "border='${border}'";	else $border='';

		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		return $anchor_open."<img src='${iURL}' $alt $width $height $border />".$anchor_close;
	}
	
} // end class definition.
//</source>