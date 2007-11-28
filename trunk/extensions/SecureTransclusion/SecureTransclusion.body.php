<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureTransclusion
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class SecureTransclusion
{
	const thisType = 'other';
	const thisName = 'SecureTransclusion';
	
	public function __construct() {}
	
	public function mg_strans( &$parser, $iwpage, $errorMessage = null, $timeout = 5 )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'SecureTransclusion: '.wfMsg('badaccess');
		
		$title = Title::newFromText( $iwpage );
		if (is_null( $title ) || (!$title->isTrans()))
			return 'SecureTransclusion: '.wfMsg("importbadinterwiki");
		
		$uri = $title->getFullUrl();
		$text = $this->fetch( $uri, $timeout );
		
		// if we didn't get succeed, turn off parser caching
		// hoping to get lucky next time around.
		if (false === $text)
		{
			$parser->disableCache();
			return $errorMessage;
		}
			
		return $text;
	}
	/**
		1- IF the page is protected for 'edit' THEN allow execution
		2- IF the page's last contributor had the 'strans' right THEN allow execution
		3- ELSE deny execution
	 */
	private static function checkExecuteRight( &$title )
	{
		global $wgUser;
		if ($wgUser->isAllowed('strans'))
			return true;
		
		if ($title->isProtected('edit'))
			return true;
		
		// Last resort; check the last contributor.
		$rev    = Revision::newFromTitle( $title );
		
		$user = User::newFromId( $rev->mUser );
		$user->load();
		
		if ($user->isAllowed( 'strans' ))
			return true;
		
		return false;
	}
	/**
	 * 
	 */
	protected function getFromCache( &$uri )
	{
		// prepare the parser cache for action.
		$parserCache =& ParserCache::singleton();

		global $wgUser;
		$parserOutput = $parserCache->get( $uri, $wgUser );

		// did we find it in the parser cache?
		if ( $parserOutput !== false )
			return $parserOutput->getText();

		return null;
	}
	/**
	 *
	 */
	protected function saveInCache( &$uri, &$text )
	{
		global $wgUser;
		
		$popts = new ParserOptions( $wgUser );

		// prepare the parser cache for action.
		$parserCache =& ParserCache::singleton();

		$parserCache->save( );
	}
	/**
	 *
	 */	
	protected function fetch( &$uri, $timeout )
	{
		// try to fetch from cache
		$text = $this->getFromCache( $uri );
		if ( $text === null)
		{
			$text = Http::get( $uri, $timeout );
			$text = $this->saveInCache( $uri, $text );
		}
		
		return $text;
	}
	
} // end class
//</source>
