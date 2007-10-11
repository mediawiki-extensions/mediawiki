<?php
/**
 * @author Jean-Lou Dupont
 * @package AutoRedirect
 * @version $Id$
 */
//<source lang=php>
class AutoRedirect
{
	const thisType = 'other';
	const thisName = 'AutoRedirect';
	
	public static $msg = array();
	
	public function __construct() 
	{
		global $wgMessageCache;
		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}
	
	public function mg_autoredirect( &$parser, &$ns = null, &$page = null, &$alternateText = null )
	{
		// if ns contains a numeric
		if (is_numeric( $ns ))
		{
			$name = Namespace::getCanonicalName( $ns );
			if (empty( $name ))
				return wfMsgForContent('autotedirect-invalid-namespace-numeric');
		}		
		else
		{
			if ( ($n = Namespace::getCanonicalIndex( strtolower($ns) )) === NULL)	
				return wfMsgForContent('autoredirect-invalid-namespace-string');				
			$ns = $n;
		}
	
		// if the source page already exists, bail out silently.
		$title   = Title::makeTitle( $ns, $page );
		$article = new Article( $title );
		if ( $article->getID() !=0 )
			return null;
			
		// the source page where the redirect should be created
		// does not exist currently. Great.
		$link = $this->createRedirectPage( $parser, $article, $alternateText );	
		
		if (!empty( $alternateText ))
			return $link;
			
		return null;
	}
	
	private function createRedirectPage( &$parser, &$article, &$alternateText )
	{
		$ns   = $parser->mTitle->getNamespace();
		$page = $parser->mTitle->getText();
		
		$nsName = Namespace::getCanonicalName( $ns );
		
		if (empty( $alternateText ))
			$text = wfMsgForContent('autoredirect-this-page');
		else
			$text = $alternateText;
			
		$link = wfMsgForContent('autoredirect-link-text', $nsName, $page, $text);
		$pageText = wfMsgForContent( 'autoredirect-page-text', $nsName, $page );
		$summary  = wfMsgForContent( 'autoredirect-summary-text', $nsName, $page, $text );
		$article->insertNewArticle( $pageText, $summary, false, false );

		return $link;	
	}
	
} // end class

require('AutoRedirect.i18n.php');
//</source>