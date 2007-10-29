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
		if ( $wgTitle->getNamespace() == NS_MEDIAWIKI )
			return true;
			
		// wrap the extended toolbox correctly.
		echo "\n".'</ul></div></div>'."\n";
		echo '<div class="portlet" id="toolbox_extended">'."\n";
		echo '<div class="pBody"><ul>'."\n";
		echo $this->getText( self::$tbPageTitle, $ns );
		
		return true;
	}
	protected function getText( $title )
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
			return $this->parse( $titleObject, $rev->getText() );
		
		return null;
	}
	protected function parse( &$title, &$text )
	{
		global $wgParser, $wgUser;
		
		// clone the standard parser just to
		// make sure we don't break something.
		$parser = clone $wgParser;
		
		$popts = new ParserOptions( $wgUser );
		$parserOutput = $parser->parse(	$text, 
										$title, 
										$popts, 
										true, true, 
										null );
		return $parserOutput->getText();
	}
} // end class

//</source>