<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>*/
require 'ImageLink.i18n.php';

class ImageLink
{
	// constants.
	const thisName = 'ImageLink';
	const thisType = 'other';
	
	var $links;

	// For Messages
	static $msg = array();

	// Error Codes
	const codeInvalidTitleImage = 0;
	const codeInvalidTitleLink  = 1;
	const codeArticleNotExist   = 2;
	const codeLinkLess          = 3;
	const codeImageNotExist		= 4;
	const codeDefaultNotProvided= 5;
	const codeMissingParameter  = 6;
	const codeEmptyList  		= 7;	
	
	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 */
	static $parameters = array(
		'image'	=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null ),
		'default'=>array( 'm' => false, 's' => false, 'l' => false, 'd' => null ),		
		'page'	=> array( 'm' => false, 's' => false, 'l' => false, 'd' => '' ),
		'alt'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null ),
		'height'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'width' => array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'alt'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'title' => array( 'm' => false, 's' => true,  'l' => true,  'd' => null  ),
		'border'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null  )
	);
	/**
	 * Initialize the messages
	 */
	public function __construct()
	{
		global $wgMessageCache;

		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}	 
	/**
	 * legacy parser function... please use #img instead
	 */
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
		if ($this->isError( $html ))
			return $this->getErrorMsg( $html );
		return array( $html, 'noparse' => true, 'isHTML' => true );		
	}
	/**
	 * Can be used with [[Extension:ParserPhase2]]
	 */
	public function mg_imagelink_raw( &$parser, $img, $page='',  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null, $title = null )// optional parameters
	{
		$html = $this->buildHTML( $img, $page, $alt, $width, $height, $border, $title );
		if ($this->isError( $html ))
			return $this->getErrorMsg( $html );
		return $html;
	}
	/**
	 * This method builds the HTML code relative to the required imagelink
	 */
	protected function buildHTML( $img, $page,  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null, $title = null )
	{
		$iURL = $this->getImageURL( $img );
		if ($this->isError( $iURL ))
			return $iURL;
		
		// prepare for 'link-less' case ... if required.
		$anchor_open = '';
		$anchor_close = '';
		
		$ret = $this->getLinkToPageAnchor( $page, $anchor_open, $anchor_close );
		if ($this->isError( $ret ) && ( $ret !== self::codeLinkLess))
			return $ret;
		
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
	 * Returns the URL of the specified Image page
	 * Reverts to default Image page IFF the title isn't an interwiki one
	 */
	protected function getImageURL( &$img, &$default = null )
	{
		$iURL = $this->getImageURLreal( $img );
		
		// try out the specified image page name and
		// revert to default if it does not exists
		if ( ($iURL===self::codeInvalidTitleImage) || ($iURL===self::codeImageNotExist) )
		{
			if ( $default === null )
				return self::codeDefaultNotProvided;
				
			// if this one fails, not much we can do...
			$iURL = $this->getImageURLreal( $default );
		}
		
		return $iURL;	
	}
	/**
	 * Really returns an URL for a given image page.
	 */
	protected function getImageURLreal( &$img )
	{
		$ititle = Title::newFromText( $img );

		// this really shouldn't happen... not much we can do here.		
		if (!is_object($ititle)) 
			return self::codeInvalidTitleImage;

		// check if we are dealing with an InterWiki link
		if ( $ititle->isLocal() )
		{
			$image = Image::newFromName( $img );
			if ( !$image->exists() ) 
				return self::codeImageNotExist;
	
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
			return self::codeLinkLess;
			
		$ptitle = Title::newFromText( $page );
		
		// this might happen in templates...
		if (!is_object( $ptitle ))
			return self::codeInvalidTitleLink;
				
		if ( $ptitle->isLocal() )
		{
			// check if the local article exists
			if ( !$ptitle->exists() )
				return self::codeArticleNotExist;
				
			$tURL = $ptitle->getLocalUrl();
			$aClass=''; 			
		}
		else
		{
			// we can't know easily what is at the end of this URL...
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
	 *			[|default=image-page-used-for-default]
	 * }} 
	 */
	public function mg_img( &$parser )
	{
		$params = func_get_args();
		
		$liste = StubManager::processArgList( $params, true );
		
		$sliste= $this->doListSanitization( $liste, self::$parameters );
		if (!is_array( $sliste ))
			return wfMsgForContent( 'imagelink'.self::codeMissingParameter, $sliste);
		
		$this->doSanitization( $sliste, self::$parameters );
		
		$html = $this->buildHTMLfromList( $sliste, self::$parameters );		
		if ($this->isError( $html ))
			return $this->getErrorMsg( $html );
					
		return array( $html, 'noparse' => true, 'isHTML' => true );			
	}
	/**
	 * @return false invalid image page title
	 * @return null  invalid target title
	 * @return -1    local article does not exist
	 */
	protected function buildHTMLfromList( &$liste, &$ref_liste )
	{
		$img_url = $this->getImageURL( $liste['image'], $liste['default'] );
		if ($this->isError( $img_url ))
			return $img_url;
		
		$page = $liste['page'];
		
		// prepare for 'link-less' case ... if required.
		$anchor_open = '';
		$anchor_close = '';
		
		$r = $this->getLinkToPageAnchor( $page, $anchor_open, $anchor_close );
		if ( $this->isError( $r ) && ( $r !== self::codeLinkLess) )
			return $r;

		$params = $this->buildList( $liste, $ref_liste );
		
		return $anchor_open."<img src='${img_url}' $params />".$anchor_close;
	}
	/**
	 * Retrieves the specified list of parameters from the list.
	 * Uses the ''l'' parameter from the reference list.
	 */
	protected function buildList( &$liste, &$ref_liste )	
	{
		if (empty( $liste ))
			return self::codeEmptyList;
			
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
			return self::codeEmptyList;
			
		foreach( $liste as $key => &$value )
		{
			if ( $ref_liste[ $key ]['s'] === true )
				$value = htmlspecialchars( $value );
		}
	}
	/**
	 * Returns 'true' if the code provided constitute an error code
	 */
	protected function isError( $code )
	{
		return is_numeric( $code );
	}
	/**
	 * Returns the corresponding error message
	 */
	protected function getErrorMsg( $code )
	{
		return wfMsg( 'imagelink'.$code );	
	}
} // end class definition.
//</source>