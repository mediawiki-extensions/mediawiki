<?php
/**
 * @author Jean-Lou Dupont
 * @package ToolboxExtender	
 * @version $Id$
 */
//<source lang=php>
class ToolboxExtender
{
	static $tbPageTitle = 'MediaWiki:Registry/ToolboxExtender';
	
	public function hMonoBookTemplateToolboxEnd( &$tpl )
	{
		global $wgTitle;
		$ns = $wgTitle->getNamespace();
		
		// can't bookmark in NS_SPECIAL namespace
		if ( NS_SPECIAL === $ns )
			return true;
		
		// wrap the extended toolbox correctly.
		echo '</ul></div></div>'."\n";
		echo '<div class="portlet" id="toolbox_extended">'."\n";
		echo $this->getText( self::$tbPageTitle, $ns );
		echo '<div class="pBody"><ul>'."\n";
		
		return true;
	}
	protected function getText( $title,
								$ns		/* future use */ )
	{
		$titleObject = Title::newFromText( $title );
		
		$article = new Article( $titleObject );
		// make sure the article exists.
		if ( $article->getId() == 0 )
			return null;
			
		// prepare the parser cache for action.
		$parserCache =& ParserCache::singleton();

		global $wgUser;
		$parserOutput = $parserCache->get( $article, $wgUser );

		// did we find it in the parser cache?
		if ( $parserOutput !== false )
			return $parserOutput->getText();

		// no... that's too bad; go the long way then.
		$rev = Revision::newFromTitle( $titleObject );
		if (is_object( $rev ))
			return $rev->getText();
			
		return null;
	}
	
} // end class

//</source>