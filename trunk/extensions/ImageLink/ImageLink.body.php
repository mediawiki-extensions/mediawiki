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

	static $parameters = array(
		'image'	=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null ),
		'page'	=> array( 'm' => false, 's' => false, 'l' => false, 'd' => '' ),
		'alt'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null ),
		'height'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'width' => array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'alt'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'title' => array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'border'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null  )
	);
	
	public function mg_imagelink( &$parser, $img, $page='',  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null, $title = null )// optional parameters
	/**
	 *  $img  = image reference i.e. a valid image name e.g. "New Clock.gif" 
	 *  $page = page reference i.e. a valid page name e.g. "Admin:Show Time"
	 *
	 * {{#imagelink:New Clock.gif|Admin:Show Time|alternate text}}
	 */
	{
		$html = $this->buildHTML( $img, $page, $alt, $width, $height, $border, $title );
		
		$t = "_imagelink_".date('Ymd').count($this->links)."_/imagelink_";
				
		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		$this->links[] = $html;

		return $t;
	}
	/**
	 * Can be used with [[Extension:ParserPhase2]]
	 */
	public function mg_imagelink_raw( &$parser, $img, $page='',  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null, $title = null )// optional parameters
	{
		return $this->buildHTML( $img, $page, $alt, $width, $height, $border, $title );
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
								$alt=null, $width=null, $height=null, $border=null, $title = null )
	{
		$iURL = $this->getImageURL( $img );
		if ( ( $iURL === null ) || ( $iURL === false) )
			return 'ImageLink: invalid image page title.';


		// prepare for 'link-less' case ... if required.
		$anchor_open = '';
		$anchor_close = '';
		
		$ret = $this->getLinkToPageAnchor( $page, $anchor_open, $anchor_close );
		if ( $ret === false )
			return 'ImageLink: invalid image page title.';
		
		// sanitize the input
		$alt    = htmlspecialchars( $alt );
		$width  = htmlspecialchars( $width );
		$height = htmlspecialchars( $height );
		$border = htmlspecialchars( $border );
		$title = htmlspecialchars( $title );		
								
		// Optional parameters
		if ($alt    !== null)	$alt    = "alt='${alt}'"; 		else $alt='';
		if ($width  !== null)	$width  = "width='${width}'"; 	else $width='';
		if ($height !== null)	$height = "height='${height}'";	else $height='';
		if ($border !== null)	$border = "border='${border}'";	else $border='';
		if ($title  !== null)	$title =  "title='${title}'";	else $title='';		

		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		return $anchor_open."<img src='${iURL}' $alt $width $height $border $title />".$anchor_close;
	}
	/**
	 * @return string Valid URL
	 * @return null for invalid image page title
	 * @return false for inexisting image page
	 */
	protected function getImageURL( &$img )
	{
		$ititle = Title::newFromText( $img );

		// this really shouldn't happen... not much we can do here.		
		if (!is_object($ititle)) 
			return null;

		// check if we are dealing with an InterWiki link
		if ( $ititle->isLocal() )
		{
			$image = Image::newFromName( $img );
			if (!$image->exists()) 
				return false;
	
			$iURL = $image->getURL();
		}
		else
			$iURL = $ititle->getFullURL();

		return $iURL;		
	}
	/**
	 * getLinkToPage
	 */
	protected function getLinkToPageAnchor( &$page, &$anchor_open, &$anchor_close )
	{
		// check if we are asked to render a 'link-less' element
		if (empty( $page ))
			return null;
			
		$ptitle = Title::newFromText( $page );
		
		// this might happen in templates...
		if (!is_object( $ptitle ))
			return false;
				
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

		return true;
	}
	
	/**
	 * {{#img:  image=image-page 
	 *			[|page=page-to-link-to] 
	 *			[|alt=alternate-text]
	 *			[|height=height-parameter]
	 *			[|width=width-parameter]	 
	 *			[|border=border-parameter]	 
	 *			[|title=title-parameter]
	 * }} 
	 */
	public function mg_img( &$parser )
	{
		$params = func_get_args();
		
		$liste = StubManager::processArgList( $params, true );
		
		$sliste= $this->doListSanitization( $liste, self::$parameters );
		if (!is_array( $sliste ))
			return "ImageLink: invalid or missing parameter ($sliste)";
		
		$this->doSanitization( $sliste, self::$parameters );
		
		$html = $this->buildHTMLfromList( $sliste, self::$parameters );		
		
		$t = "_imagelink_".date('Ymd').count($this->links)."_/imagelink_";
				
		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		$this->links[] = $html;
		
		return $t;
	}
	protected function buildHTMLfromList( &$liste, &$ref_liste )
	{
		$img_url = $this->getImageURL( $liste['image'] );
		$page = $liste['page'];
		
		// prepare for 'link-less' case ... if required.
		$anchor_open = '';
		$anchor_close = '';
		$this->getLinkToPageAnchor( $page, $anchor_open, $anchor_close );

		$params = $this->buildList( $liste, $ref_liste );
		
		return $anchor_open."<img src='${img_url}' $params />".$anchor_close;
	}
	protected function buildList( &$liste, &$ref_liste )	
	{
		if (empty( $liste ))
			return null;
			
		$result = '';
		// only pick the key:value pairs that have been
		// explictly marked using the 'l' key in the
		// reference list.
		foreach( $liste as $key => &$value )
			if ( $ref_liste[ $key ]['l'] === true )
				$result .= " $key='$value'";

		return $result;		
	}
	/**
	 * Sanitize the parameters list. 
	 * Just keeps the parameters defined in the reference list.
	 */
	protected function doListSanitization( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return array();

		// first, let's make sure we only have valid parameters
		$new_liste = array();
		foreach( $liste as $key => &$value )
			if (isset( $ref_liste[ $key ] ))
				$new_liste[ $key ] = $value;
				
		// then make sure we have all mandatory parameters
		foreach( $ref_liste as $key => &$instructions )
			if ( $instructions['m'] === true )
				if ( !isset( $liste[ $key ] ))
					return $key;
					
		// finally, initialize to default values the missing parameters
		foreach( $ref_liste as $key => &$instructions )
			if ( $instructions['d'] !== null )
				if ( !isset( $new_liste[ $key ] ))
					$new_liste[ $key ] = $instructions['d'];
				
		return $new_liste;
	}
	/**
	 * Only valid parameters should end-up here.
	 */
	protected function doSanitization( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return;
			
		foreach( $liste as $key => &$value )
		{
			if ( $ref_liste[ $key ]['s'] === true )
				$value = htmlspecialchars( $value );
		}
	}
	
} // end class definition.
//</source>