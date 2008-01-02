<?php
/**
 * @author Jean-Lou Dupont
 * @package TagToTemplate
 * @version 1.0.3
 * @Id $Id: TagToTemplate.body.php 797 2008-01-01 21:08:35Z JeanLou.dupont $ 
 */
// <source lang=php>
class TagToTemplate
{
	// 
	static $tablePageName = 'MediaWiki:Registry/TagToTemplate';
	static $open_pattern =  '/\<$tag(.*)\>/siU';
	static $close_pattern = '</$tag>';
	static $open_replace = '{{$tag|$params|';
	static $close_replace = '}}';
	
	var $loaded = false;
	var $map = array();
	
	public function __construct() {}
	
	/**
	 * Helper function that helps us populate the 'map' table.
	 * This parser function should be used in the 'Table' page
	 * referenced through self::$tablePageName
	 */
	public function mg_tag_to_template( &$parser, $tag, $template )
	{
		$this->map[ $tag ] = $template;
	}
	/**
	 * Do the substitute before MediaWiki's parser as a chance
	 * to parse the actual text.
	 */
	public function hParserBeforeStrip( &$parser, &$text, &$strip_state )
	{
		$this->loadTable();		
		$this->substitute( $text );
		return true;		
	}
	/**
	 * Loads the mapping table.
	 */
	private function loadTable()
	{
		// no need to load multiple times.
		if ($this->loaded)
			return;
		
		$this->loaded = true;
					
		$title = Title::newFromText( self::$tablePageName );
		if ( !$title->exists() )
			return;
			
		$tablePageRev = Revision::newFromTitle( $title );
		
		if (is_object( $tablePageRev ))
		{
			$tablePage = $tablePageRev->getText();
			
			// use the global parser to parse the page in question.
			//global $wgParser;
			//$parser = clone $wgParser;
			global $wgUser;
			$parser = new Parser;
			$parser->setFunctionHook( 'tag_to_template', array( $this, 'mg_tag_to_template' ) );
			
			// this will populate the 'map' variable
			// assuming of course that the page was edited with
			// {{#tag_to_template| ... }} instructions.
			$parser->parse( $tablePage, $title, new ParserOptions( $wgUser) );
		}
	}
	/**
	 * Performs the 'tag to template' substitution.
	 */
	private function substitute( &$text )
	{
		if (empty( $this->map ) || empty( $text ) )	
			return;

		foreach( $this->map as $tag => $template )
		{
			$this->replaceOpen( $tag, $template, $text );	
			$this->replaceClose( $tag, $text );
		}
	}
	/**
	 * Replaces all the 'open' tags e.g. < taghere paramshere >
	 * The parameters are passed as {{{1}}} variable in the resulting template.
	 */
	private function replaceOpen( &$tag, &$template, &$text )	
	{
		$p = str_replace('$tag', $tag, self::$open_pattern );
		
		$r = preg_match_all( $p, $text, $m );
		// make sure we have some entries.
		if ( ($r===0) || ($r===false))
			return;

		// base open replace pattern
		$orb = str_replace('$tag', $template, self::$open_replace );
		
		foreach( $m[0] as $index => $full_match )
		{
			// prepare the parameters substitution.
			$params = $m[1][$index];
			$or = str_replace( '$params', $params, $orb );
			
			// do the actual full substitution
			$text = str_replace( $full_match, $or, $text);
		}
	}
	/**
	 * Replaces all the 'close' tags e.g. < /taghere >
	 */
	private function replaceClose( &$tag, &$text )
	{
		$p = str_replace( '$tag', $tag, self::$close_pattern );
		$text = str_replace( $p, self::$close_replace, $text );
	}
} // end class

//</source>