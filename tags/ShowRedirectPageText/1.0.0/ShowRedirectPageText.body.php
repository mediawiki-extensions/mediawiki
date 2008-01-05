<?php
/**
 * @author Jean-Lou Dupont
 * @package ShowRedirectPageText
 * @version 1.0.0
 * @Id $Id: ShowRedirectPageText.body.php 840 2008-01-05 02:31:00Z jeanlou.dupont $
 */
//<source lang=php>*/
class ShowRedirectPageText
{
	const defaultAction = true;   // by default, show the text
	
	const thisName = 'ShowRedirectPageText';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	var $found;
	var $actionState;

	public function __construct() 
	{
		$this->found = null;
		$this->actionState = self::defaultAction;
	}

	public function setActionState( $s ) { $this->actionState = $s ;}

	public function hArticleViewHeader( &$article )
	{
		// check if we are dealing with a redirect page.
		$this->found = Title::newFromRedirect( $article->getContent() );
		
		return true;		
	}
	public function hOutputPageParserOutput( &$op, $parserOutput )
	{
		// are we dealing with a redirect page?
		if ( ( !is_object($this->found) ) || ( !$this->actionState ) )return true;
	
		// take care of re-entrancy
		if ( !is_object($this->found) ) return true;
		$this->found = null;
		
		$op->addParserOutput( $parserOutput );
		return true;	
	}
	
} // end class definition.
//</source>