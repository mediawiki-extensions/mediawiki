<?php
/*
 * ObjectClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
*/

class ObjectCollectionClass
{
	// collection.
	var $coll;
	var $filter;
	var $contextFilter;
		
	static function getGlobalObjectName() { return "objCo";           }
	static function &getGlobalObject()    { return $GLOBALS['objCo']; }	
	
	function pset( &$text, &$argv, &$parser)  
	{	
		// Bypass init phase.
		$parser->setHook( "obj",    array($this, 'set') );
		$parser->setHook( "objval", array($this, 'pget') );
		return $this->set( $text, $argv, $parser );
	}
	
	public function set	( &$text, &$argv, &$parser )
	{
		if (empty($text)) return '';

		// use the complete article name as context.
		// This is useful when multiple article are loaded
		// during one transaction.
		$context = $parser->mTitle->getPrefixedDBkey();

		if (isset($argv['template']))
			$o = $this->processTemplate( $text, $argv['template'] );
		else
			$o = new SimpleXMLElement($text);

		if (!empty($o))
			$this->coll[$context][] = $o;
		
		return '';	  
	}
	
	private function processTemplate( &$t, &$pn )
	{
		// template reference stored in article page $pn

		// Build ourself a Title Object in order to interface
		// properly with the Parser object.
		global $mediaWiki;
		
		$title = Title::newFromText( $pn );
		if ($title == null)
			return null;
  
  		$article = $mediaWiki->articleFromTitle($title);
	
		if ($article == null )
			return null;
	
		$article->loadContent();

  # if no page or an empty one
  if (!$article->mDataLoaded)
  	return null;
	 
  return $article->mContent;

			
		// At this point, the page contains at least some data.
		// Parse it and we'll get all the tagged data in the
		// global TagClass object.			
		$po = $this->parser->parse( $content, $title, $this->parserOptions );
		
	}
	
	public function pget( &$text, &$argv, &$parser)
	{
		// context/obj id=xyz
		//
		$id  = $argv['id'];  if (empty($id)) $id = 0;
		$key = $argv['key']; if (empty($key)) return '';
		$cxt = $argv['context']; if (empty($cxt)) $cxt = $parser->mTitle->getPrefixedDBkey(); 

		return $this->get( $cxt, $id, $key );		
	}
	
	public function get( $context, $id, $key )
	{
		if (isset($this->coll[$context]))
		{
			$o = $this->coll[$context][$id];
			if (!is_object( $o ))
				return $o;

			return $o->$key;
		}
		return '';
	}
}

?>