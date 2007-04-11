<?php
/*
 * ArticleCacheClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
*/

class ArticleCacheClass
{
	public static function &singleton() 
	{
		static $instance;
		if ( !isset( $instance ) ) 
			$instance = new ArticleCacheClass( );
		return $instance;
	}

	public function ArticleCacheClass() {}

	// deprecated interface: use "singleton" functionality.
	static function getGlobalObjectName() { return "acGlobalObj";           }
	static function &getGlobalObject()    { return $GLOBALS['acGlobalObj']; }	

	var $acache = array();
	
	/* CAUTION: $article_title is *not* a Mediawiki object of class "Title"
	 * ======== but rather a fully qualified title name e.g.
	 *          main:page 
	 */
	function &getArticleContent( $article_title )
	{
		// check our local cache
		if (isset( $this->acache[$article_title] ))
			return $this->acache[$article_title];

		$article = $this->getArticle( $article_title );
		
		// Also, if article can't be found, bail out.
		if ($article == null )
			return null;
		
		// Let's try fetching the page content.
		$article->loadContent();
		
		# if no page or an empty one
		if (!$article->mDataLoaded)
			return null;
		
		$this->putInCache( $article_title, $article->mContent );
		
		return $this->acache[$article_title];
	}
	public function getArticle( $article_title )
	{
		$title = Title::newFromText( $article_title );
		  
		// Can't load page if title is invalid.
		if ($title == null)
			return null;
		
		$article = new Article($title);

		return $article;	
	}
	
	private function putInCache( $article_title, &$content )
	{
		$this->acache[$article_title] = $content;
	}
	
	/*
	 *  Input: Mediawiki Title Object
	 */
	static public function makeTitleName( $title )
	{
		$ns = $title->getNamespace();
		$pn = $title->getDbKey();
	
		return Namespace::getCanonicalName($ns).":".$pn;
	}

	public function &recurseGetArticleContent( $base_title, $target, $level = 0 )
	{
		$c = null;
		$a = $this->recurseGetArticle( $base_title, $target, $level );
		if (empty($a)) return null;
		if ($a->getID()!=0)
		{			
			$name = $a->mTitle->getPrefixedDBkey();
			// check our local cache
			if (isset( $this->acache[$name] ))
				return $this->acache[$name];

			$c = $a->getContent();
			$this->putInCache( $name, $c);
		}
		return $c;
	}
	public function recurseGetArticle( $base_article, $target_title, $level = 0 )
	/*
	 *   Purpose:  hierarchical walk upwards to find the required subpage.
	 */
	{
		// $base_article: MW prefixed name
		// $target_title: MW name
		// $level = 0 => means recurse all the way to the namespace top.

		#  //level0/level1/level2 ...
		#  ... base/target
		#  //level0/level1/base/target
		#  //level0/level1/target
		#  //level0/target
		#  //target
		
		// First, figure out how many levels we have in the $base_article in question.
		// Count the number of '/'
		
		$e = explode("/", $base_article );
		$blCount = count($e);

		if ($level>$blCount)
			$level = $blCount;

		// Next, figure out how many levels we are asked to walk
		if ($level == 0)
			$count = $blCount;
		else
			$count = $level;

		$a = null;		
		do
		{
			$base = implode($e, "/");
			// build the article prefixed name
			$name = $base."/$target_title";
			$a = $this->getArticle( $name );
			if ($a->getID()!=0)
				break;
			// we did not find an article this time around,
			// let's trim the base name and try again.
			unset( $e[$count-1] );			
		} while(--$count);

		// try the top level case
		// First get namespace
		$f = explode(":", $base_article );
		
		// If namespace present, then use it
		if (count($f)>1)
			$target_title = $f[0].":".$target_title;

		// one last try provided we have failed before.
		if ($a->getID()==0)	
			$a=$this->getArticle( $target_title );

		return $a;
	}	
} // End class definition.
?>