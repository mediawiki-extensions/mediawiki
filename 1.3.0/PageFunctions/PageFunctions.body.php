<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage PageFunctions
 * @version 1.3.0
 * @Id $Id: PageFunctions.body.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>*/
class PageFunctions
{
	const thisName = 'PageFunctions';
	const thisType = 'other';

	var $pageTitle;
	var $pageTitleHTML;
	
	var $pageVars;

	// Our class defines magic words: tell it to our helper class.
	public function __construct()
	{	
		$this->pageVars = array();
		$this->pageTitle = null;
		$this->pageTitleHTML = null;
	}

	// ===============================================================
	public function mg_pagetitle( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		return $this->setTitle( $params[0] );
	}
	private function setTitle( &$title )
	{
		$this->pageTitle = trim( $title );
		global $wgOut;
		$wgOut->setPageTitle( $title );
		$this->pageTitleHTML = $wgOut->getHTMLTitle();
	}

	// ===============================================================
	public function mg_pagesubtitle( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		$this->setSubTitle( $params[0] );
	}
	private function setSubTitle( &$title )
	{
		global $wgOut;
		$wgOut->setSubtitle( $title );
	} 

	// ===============================================================
	public function mg_pageexists( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		return $this->doesPageExists( $params[0] );
	}

	private function doesPageExists( &$title ) 
	{
		$a = StubManager::getArticle( $title );
		if (is_object($a)) 
			$id=$a->getID();
		else $id = 0;
		
		return ($id == 0 ? false:true);		
	}

	// ===============================================================
	/**
	 * Required in order to make sure that when a page title is cleared
	 * it stays cleared. This functionality offsets what is done
	 * in Article.php when a page title is cleared.
	 */
	function hBeforePageDisplay( &$op )
	{
		if ($this->pageTitle !== null)
		{
			$op->setPageTitle( $this->pageTitle );			
			$op->setHTMLTitle( $this->pageTitleHTML );
		}
			
		return true;
	}
	/**
		Hook based Page Variable 'get'
		%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	 */
	public function hPageVarGet( &$varname, &$value )
	{
		$value = @$this->pageVars[ $varname ];		
		return true; // continue hook-chain.
	}
	/**
		Hook based Page Variable 'set'
		%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	 */
	public function hPageVarSet( &$varname, &$value )
	{
		$this->pageVars[ $varname ] = $value;		
		return true; // continue hook-chain.
	}
	public function mg_varset( &$parser ) 
	{
		$params = StubManager::processArgList( func_get_args(), true );
		$this->pageVars[ $params[0] ] = $params[1];		
	}
	public function mg_varget( &$parser ) 
	{
		$params = StubManager::processArgList( func_get_args(), true );
		return @$this->pageVars[ $params[0] ];		
	}
	/**
		Captures a variable
		
		Useful when building complex HTML pages.
		
		{{#varcapset: variable name|value }}
		((#varcapset: variable name|value )) 
	 */
	public function mg_varcapset( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		@$this->pageVars[ $params[0] ] = $params[1];
		return $params[1];
	}	 
	/**
		Sets a variable to an array.
		param 0: variable name
		param 1: array key
		param 2: array value corresponding to key.
	 */
	public function mg_varaset( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		@$this->pageVars[ $params[0] ][ $params[1] ] = $params[2];		
	
	}
	/**
		Gets a variable to an array.
		param 0: variable name
		param 1: array key
	 */
	public function mg_varaget( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		return @$this->pageVars[ $params[0] ][ $params[1] ];		
	}
	// ===============================================================
	public function mg_cshow( &$parser, &$group, &$text )
	// Conditional Show: if user is part of $group, then allow for '$text'
	// Parser Cache friendly of 'ConditionalShowSection' extension.
	{
		global $wgUser;
		$g = $wgUser->getEffectiveGroups();
		if (in_array( $group, $g ))
			return $text;
	}
	
	/**
		Magic Word 'noclientcaching'

		The actual action of disabling the client caching process is already performed through
		'ParserCache2' extension when processing 'magic words' such as this one (($noclientcaching$)).
		If on the contrary this function is called through the usual {{noclientcaching}} statement, then
		1) If 'parser caching' is used, this statement will have limited effect
		2) If 'parser caching' is not used, then this statement will have an effect everytime the page is visited.
	 */
	public function MW_noclientcaching( &$parser, &$varcache, &$ret )
	{
		global $wgOut;
		$wgOut->enableClientCache(false);
	}

	public function mg_noext( &$parser, $pagename = null)
	{
		if (empty( $pagename ))	
			return null;
		return substr( $pagename, 0, strpos( $pagename, '.' ) );	
	}
	/**
	 * Returns the url for viewing a specified page from the NS_IMAGE namespace
	 */
	public function mg_imgviewurl( &$parser, $pagename )
	{
		$imageTitle = Title::makeTitleSafe("Image", $pagename );
		if( !is_object( $imageTitle))
			return wfMsg('badtitle');

		$img = Image::newFromTitle( $imageTitle );
		if( $img->exists() != true)
			return wfMsg('badtitle');

		return $img->getViewURL(false);
	}
} // end class	
//</source>