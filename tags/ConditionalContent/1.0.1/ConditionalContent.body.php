<?php
/**
 * @author Jean-Lou Dupont
 * @package ConditionalContent
 * @version 1.0.1
 * @Id $Id: ConditionalContent.body.php 807 2008-01-02 20:30:46Z jeanlou.dupont $
 */
//<source lang=php>
require 'ConditionalContent.i18n.php';

class ConditionalContent
{
	static $msg = array();

	public function __construct()
	{
		global $wgMessageCache;
		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}
	
	/**
	 * @param string $page_true article page name to include *if* condition evaluates to 'true'
	 * @param bool $condition
	 * @param string $page_false article page name to include *if* condition evaluates to 'false'
	 */
	public function mg_cc(	&$parser, 
							$page_true, 
							/* optional parameters */
							$condition = true, /* defaults to true => include content */
							$comparator = true,
							$page_false = null /*optional*/ )
	{
		$ret = $this->process( $page_true, $condition, $comparator, $page_false );
		if ( !is_object( $ret ))
			return $ret;
			
		return $this->getPageContents( $ret );
	}
	/**
	 * Same behavior as for ''#cc'' parser function *BUT*
	 * intended to be called through [[Extension:ParserPhase2]]
	 */
	public function mg_ccd(	&$parser, 
							$page_true, 
							/* optional parameters */
							$condition = true, /* defaults to true => include content */
							$comparator = true,
							$page_false = null /*optional*/ )
	{
		$ret = $this->process( $page_true, $condition, $comparator, $page_false );
		if ( !is_object( $ret ))
			return $ret;
			
		return $this->getParsedPageContents( $ret );
	}
	/**
	 * Returns a string upon error OR Title object if OK
	 *
	 * @return string|object
	 */
	protected function process( &$page_true, &$condition, &$comparator, $page_false )
	{
		$result = ($condition == $comparator ); 
		$page =  $result ? $page_true:$page_false;
		
		// if no 'page_false' should be shown, then bail out.
		if ( ($result === false) && ( empty( $page_false ) || ($page_false === null)))
			return null;
		
		$title = null;
		
		// make sure that the requesting user has the proper right
		// to include either the 'page_true' or 'page_false' page ...
		global $wgUser;		
		$permission = $this->checkPermission( $wgUser, $page, $title );
		if ( $permission === false )
			return wfMsg( 'conditionalcontent_readrestriction' );
			
		if ( $permission === null )
			return wfMsg( 'conditionalcontent_invalidpagetitle' );
			
		return $title;
	}
	/**
	 * Check if the user has the 'read' right to the target page.
	 */	
	protected function checkPermission( &$user, &$page, &$title )
	{
		$title = Title::newFromText( $page );
		if ( !is_object( $title ))
			return null;

		return $title->userCan( 'read' );
	}	 
	/**
	 * Retrieves a local page. 
	 * Returns 'null' upon error OR the page's content if OK.
	 *
	 * @return null|string
	 */
	protected function getPageContents( &$title )
	{
		$contents = null;
		$rev = Revision::newFromTitle( $title );
		if( is_object( $rev ) )
		    $contents = $rev->getText();		
		return $contents;		
	}
	/**
	 * Returns the parsed content of a page.
	 * Checks the parser-cache before going to the lengthy process
	 * of parsing the page.
	 */
	protected function getParsedPageContents( &$title )
	{
		global $wgUser;
		$article = new Article( $title );
				
		$parserCache =& ParserCache::singleton();
		$parserOutput = $parserCache->get( $article, $wgUser );
		
		// if we succed, we just need to return the 'text'
		// because it is already parsed.
		if ( $parserOutput !== false )
			return $parserOutput->getText();								
		
		// too bad, we need to parse the page.
		$text = $this->getPageContents( $title );

		// try not to mess things around too much		
		global $wgParser;
		$parser = clone $wgParser;
		$parseout = $parser->parse($text, $title, ParserOptions::newFromUser($wgUser));		
		
		return $parseout->getText();
	}
	
} // end class
//</source>
