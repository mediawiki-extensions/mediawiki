<?php
/*
 * TagClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
*/

class TagClass
{
	var $pageTags = array();
	
	static function getGlobalObjectName() { return "tagObj";           }
	static function &getGlobalObject()    { return $GLOBALS['tagObj']; }	

	/*
	 * Initialise with a specific parser.
	*/
	function TagClass( &$parser=null )
	{
		if ( $parser !==null )
		{
			$parser->setHook( "tag",     array($this, "pset") );
			$parser->setHook( "tagset",  array($this, "pset") );
			$parser->setHook( "tagget",  array($this, "gset") );
		}
	}

	/*
	 * Inter-extension get/set interface.
	*/
	public function countAll( )		{ return count($this->pageTags); }
	public function getAll( )		{ return $this->pageTags; }
	public function get( $page, $tag = null)     
	{
		if ($tag != null)
			return $this->pageTags[$page][$tag];
		else
			return $this->pageTags[$page];
	}
	
	/*
	 * If the key does not exist, create it.
	*/
	public function set( $page, $tag, $v) 
	{ 
		$this->pageTags[$page][$tag] = $v;   
	}	

	/*
	 * Parser Set interface
	 * 
	 * <tagset [param] [key=value]> content </tagset>
	*/
	public function pset( $text, $argv, &$parser )
	{
		$page_title = TagClass::getTitleName( $parser );
		if (empty( $page_title ))
			return;
		
		if (empty($this->pageTags[$page_title]))
			$this->pageTags[$page_title] = array();
		
		$k = array_keys( $argv );
			
		$this->pageTags[$page_title] = array_merge($this->pageTags[$page_title], array($k[0] => $text ) );
	}
	/*
	*  Parser Get interface.
	*
	*  <tagget key />
	*/
	public function pget( $text, $argv, &$parser )
	{
		$page_title = TagClass::getTitleName( $parser );
		if (empty( $page_title ))
			return null;
		
		$k = array_keys( $argv );
		return $this->pageTags[$page_title][$k[0]];
	}
	
	/*
	 * Extract the fully qualified MW page name
	 * i.e. Namespace:Page
	*/	
	static private function getTitleName( &$parser )
	{
		$ns = $parser->mTitle->getNamespace();
		$pn = $parser->mTitle->getDbKey();
		
		return Namespace::getCanonicalName($ns).":".$pn;
	}

} // End class definition.
?>