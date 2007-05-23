<?php
/*
 * HeaderFooterClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 */

class HeaderFooterClass
{
	var $done; // v1.12
	var $cache;
	var $level;
	var $inproc;
	
	var $nsPar = array();

	public static function &singleton() 
	{
		static $instance;
		if ( !isset( $instance ) ) 
			$instance = new HeaderFooterClass( );
		return $instance;
	}
		 
	// deprecated interface: use "singleton" functionality.		 
	static function getGlobalObjectName() { return "hfObj";           }
	static function &getGlobalObject()    { return $GLOBALS['hfObj']; }	
	
	public function HeaderFooterClass()
	{
		$this->cache = &ArticleCacheClass::singleton();
		$this->level = 0 ;
		$this->done = false;	
	}
	/*
	 *   Parameters:  
	 *   ==========
	 *   Enable => true/false
	 *   Level  => 0, 1, 2 etc.
	 *             IF 0 THEN recurse to top of namespace (top:  NS:header, NS:footer)
	 *             IF 1 THEN just check the current level e.g. NS:base/header, NS:base/footer 
	 */
	public function setNsParams( $ns, $p ) { $this->nsPar[$ns] = $p; }
	
	public function hAddHeaderFooter( &$article, &$content )
	{
		global $action;

		// only show up the header/footer on page views		
		if ($action != 'view') return true;

		if ( $this->done ) return true;
		$this->done = true; 

		// check the per-namespace enable/disable attribute.
		$ns = $article->mTitle->getNamespace(); 
		if (!$this->nsPar[$ns]['enable']) return true;

		// Re-entrancy check
		// If this function is called recursively,
		// that probably means a processor code page 
		// is being fetched. Get out.
		if ($this->inproc) return true; 
		$this->inproc= true;		

		$name = $article->mTitle->getPrefixedDBkey();
		
		// Check if the title name ends with either "header" or "footer"
		// and skip if true.
		$e = explode("/", $name);
		$t = $e[count($e)-1];
		$u = strtolower($t);
		
		// take care of namespace prefix if present.
		$v=explode(":", $u);
		if (count($v)>1)
			$u = $v[1];

		$hdisable=false;
		$fdisable=false;
		
		// check for disabling directives.
		if (strpos($content, "<noheader/>")!==false) $hdisable=true;
		if (strpos($content, "<nofooter/>")!==false) $fdisable=true;
		if (strpos($content, "__NOHEADER__")!==false) $hdisable=true; // v1.13
		if (strpos($content, "__NOFOOTER__")!==false) $fdisable=true; // v1.13
		
		$content = preg_replace('/<noheader\/>/si','', $content);
		$content = preg_replace('/<nofooter\/>/si','', $content);		
		$content = preg_replace('/__NOHEADER__/si','', $content); // v1.13
		$content = preg_replace('/__NOFOOTER__/si','', $content); // v1.13		
		
		if ($u<>"header" and $u<>"footer")
		{	
			if (!$hdisable)
				$h = $this->cache->recurseGetArticleContent($name, "Header", $this->nsPar[$ns]['level'] );
			if (!$fdisable)				
				$f = $this->cache->recurseGetArticleContent($name, "Footer", $this->nsPar[$ns]['level'] );
	
			if (!empty($h)) $h = preg_replace( '/<noinclude>.*<\/noinclude>/si', '', $h );
			if (!empty($f)) $f = preg_replace( '/<noinclude>.*<\/noinclude>/si', '', $f );
	
			$content = $h.$content.$f;
		}		
		// RESET re-entrancy flag.
		$this->inproc= false;
		
		return true;		
	}
	public function hArticleSave( &$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags )
	// V1.14 enhancement.
	{
		global $wgParserCacheType;
				
		// check the per-namespace enable/disable attribute.
		// If the extension is enabled in this namespace, then proceed.
		$ns = $article->mTitle->getNamespace(); 
		if (!$this->nsPar[$ns]['enable'])
			return true;

		// disable the parser cache for this transaction.
		// The hack below will affect the method Article::editUpdates
		// into not saving the current article to a potential real cache.
		// BUT, once the article is viewed, the article will then be stored in the real cache.	
		$wgParserCacheType = CACHE_NONE;
		$apc =& wfGetParserCacheStorage();
		
		$pc = & ParserCache::singleton();
		$pc->mMemc = $apc;

		return true;
	}
} // END CLASS DEFINITION
?>