<?php
/*
 * ImageLinkClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 */
class ImageLinkClass extends ExtensionClass
{
	var $links;
	var $hookInPlace;
	
	static $mgwords = array( 'imagelink' );

	public static function &singleton( )
	{ return parent::singleton( ); }
	
	// Our class defines magic words: tell it to our helper class.
	public function ImageLinkClass() 
	{
		parent::__construct( self::$mgwords );
		
		$this->hookInPlace = false;
	}
	
	public function mg_imagelink( &$parser, $img, $page,  							// mandatory parameters  
								$alt=null, $width=null, $height=null, $border=null )// optional parameters
	/*
	 *  $img  = image reference i.e. a valid image name e.g. "New Clock.gif" 
	 *  $page = page reference i.e. a valid page name e.g. "Admin:Show Time"
	 *
	 * {{#imagelink:New Clock.gif|Admin:Show Time|alternate text}}
	 */
	{
		$image = Image::newFromName( $img );
		if (!$image->exists()) return;
		
		if (empty($page)) return;
			
		$title = Title::newFromText( $page );
		if (!is_object($title)) return;
		
		$iURL = $image->getURL();
		$tURL = $title->getLocalUrl();
		
		// Optional parameters
		if ($alt    !== null)	$alt    = "alt='${alt}'"; 		else $alt='';
		if ($width  !== null)	$width  = "width='${width}'"; 	else $width='';
		if ($height !== null)	$height = "height='${height}'";	else $height='';
		if ($border !== null)	$border = "border='${border}'";	else $border='';

		// just place the hook when we really need it.		
		if (!$this->hookInPlace)
		{
			global $wgHooks;	
			$wgHooks['OutputPageBeforeHTML'][]= array($this, 'hBeforeHTML');
			$this->hookInPlace = true;
		}			
		
		// let's put an easy marker that we can 'safely' find once we need to render the HTML
		$t = $this->links[] = "<imagelink><a href='${tURL}'><img src='${iURL}' $alt $width $height $border /></a></imagelink>";
		return $t;
	}

	public function hBeforeHTML( $op, &$text )
	/*
	 *  This function is called just before the HTML is rendered to the client browser.
	 */
	{
		// Some substitution to do?
		if (empty($this->links)) return;
		
		foreach($this->links as $index => $link)
		{
			// our marker will have been escaped by the MW parser.
			$l = htmlspecialchars($link);
			// This is what our marker looks like without the escaping.
			$p = "/\<\/?imagelink\>/si";
			$r = preg_replace($p,"", $link);
			$text = str_ireplace($l, $r, $text);
		}
	
		return true; // v1.3 fix.
	}
} // end class definition.
?>