<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureTransclusion
 * @version 2.0.2
 * @Id $Id$
 */
//<source lang=php>
class SecureTransclusion {
	
	const thisType = 'other';
	const thisName = 'SecureTransclusion';
	
	const pageCacheTimeout = 86400; // 1day
	
	public function mg_strans( &$parser, $page, $errorMessage = null, $timeout = 5 ) {
		
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'SecureTransclusion: '.wfMsg('badaccess');
		
		$title = Title::newFromText( $page );
		if (!is_object( $title ))
			return 'SecureTransclusion: '.wfMsg('badtitle')." ($page)";
		
		if ( $title->isTrans() )
			$content = $this->getRemotePage( $parser, $title, $errorMessage, $timeout );
		else
			$content = $this->getLocalPage( $title, $errorMessage );
			
		$po = $parser->parse( $content, $parser->mTitle, new ParserOptions() );
		$html = $po->getText();
			
		return array( $html, 'noparse' => true, 'isHTML' => true );
	}
	/**
	 * Retrieves a local page.
	 */
	protected function getLocalPage( &$title, $error_msg ) {
		
		$contents = $error_msg;
		$rev = Revision::newFromTitle( $title );
		if( is_object( $rev ) )
		    $contents = $rev->getText();		
		return $contents;		
	}
	/**
	 * Retrieves a page located on a remote server.
	 */
	protected function getRemotePage( &$parser, &$title, &$error_msg, $timeout ) {
		
		$uri = $title->getFullUrl();

		$text = $this->fetch( $uri, $timeout );
		
		// if we didn't succeed, turn off parser caching
		// hoping to get lucky next time around.
		if (false === $text)
		{
			$parser->disableCache();
			$text = $error_msg;
		}
			
		return $text;
	}	 
	/**
	 * Verifies if the current user can execute the parser function
	 */
	private static function checkExecuteRight( &$title ) {
		
		return ( $title->isProtected('edit') );
	}
	/**
	 *  Fetches an external page from either the parser cache or external uri
	 *  This extension uses the services of [[Extension:PageServer]] to this end
	 */	
	protected function fetch( $uri, $timeout ) {
		
		$page     = false;
		$etag     = null;
		$source   = null;
		$state    = null;
		
		wfRunHooks( 'page_remote', array( $uri, &$page, &$etag, &$source, &$state, $timeout ) );
		
		return $page;
	}
} // end class
//</source>
