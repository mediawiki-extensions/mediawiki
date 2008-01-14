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
	const codeRestrictedParam   = 8;
	
	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 */
	static $parameters = array(
		'image'		=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null ),
		'default'	=>array( 'm' => false, 's' => false, 'l' => false, 'd' => null ),		
		'page'		=> array( 'm' => false, 's' => false, 'l' => false, 'd' => '' ),
		'alt'		=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true ),
		'height'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'width' 	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'alt'		=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'title' 	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'border'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),

		// events
		'onchange'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onsubmit'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onreset'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onselect'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),

		'onblur'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onfocus'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		
		'onkeydown'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onkeyup'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onkeypress'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),

		'onclick'	=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'ondblclick'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),

		'onmousedown'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onmousemove'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onmouseout' => array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onmouseover'=> array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
		'onmouseup'	 => array( 'm' => false, 's' => true,  'l' => true,  'd' => null, 'dq' => true  ),
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
	 *			[|onchange=onchange-handler]
	 *			[|onsubmit=onsubmit-handler]	 
	 *			[|onreset=onreset-handler]	 
	 *			[|onselect=onselect-handler]	 
	 *			[|onblur=onblur-handler]	 
	 *			[|onfocus=onfocus-handler]	 
	 *			[|onkeydown=onkeydown-handler]	 
	 *			[|onkeyup=onkeyup-handler]	 
	 *			[|onkeypress=onkeypress-handler]	 
	 *			[|onclick=onclick-handler]	 
	 *			[|ondblclick=ondblclick-handler]
	 *			[|onmousedown=onmousedown-handler]
	 *			[|onmousemove=onmousemove-handler]	 
	 *			[|onmouseout=onmouseout-handler]	 	 
	 *			[|onmouseover=onmouseover-handler]	 	 	 
	 *			[|onmouseup=onmouseup-handler]	 	 	 
	 * }} 
	 */
	public function mg_img( &$parser )
	{
		$params = func_get_args();
		
		$liste = StubManager::processArgList( $params, true );
		
		$sliste= ExtHelper::doListSanitization( $liste, self::$parameters );
		if (!is_array( $sliste ))
			return wfMsgForContent( 'imagelink'.self::codeMissingParameter, $sliste);
		
		ExtHelper::doSanitization( $sliste, self::$parameters );
		
		$result = ExtHelper::checkListForRestrictions( $sliste, self::$parameters );
		$title  = $parser->mTitle;
		
		// first check for restricted parameter usage
		$check = $this->checkRestrictionStatus( $title, $result );
		if ($this->isError( $check ))
			return $this->getErrorMsg( $html, $check );
		
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

		$params = ExtHelper::buildList( $liste, $ref_liste );
		
		return $anchor_open."<img src='${img_url}' $params />".$anchor_close;
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
	protected function getErrorMsg( $code, $param = null )
	{
		return wfMsgForContent( 'imagelink'.$code, $param );	
	}
	/**
	 * 
	 */
	protected function checkRestrictionStatus( &$title, $result )
	{
		$protected = $title->isProtected('edit');

		// if the page is protected, then anything goes!
		if ( $protected )
			return false;
		
		// page is not protected... are there any restricted parameters then?
		return ( $result !== false ) ? self::codeRestrictedParam:false;
	}
} // end class definition.
//</source>