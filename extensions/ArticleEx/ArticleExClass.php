<?php
/*
 * ArticleExClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 */

require_once("$IP/includes/Article.php");

class ArticleExClass extends Article
{
	static $qType = array(
							'js'  => array( 'js', 'javascript' ),
							'xsl' => array( 'xsl', 'xml' ),
							'xml' => array( 'xml', 'xsl' ),
						);
	var $noskin;
	var $extractSection;
	var $type;
	var $bname;
	var $fname;
	var $section;
	var $attributes;
	var $foundDot;

	public function ArticleExClass( &$title, &$article, $firstArticleInTransation=false )
	{
		$this->init();	
		$this->setA("first", $firstArticleInTransation );  // new in v1.2
		$this->create( $title, $article );
	}
	
	public function init()
	{
		$this->noskin = false;
		$this->type   = null;
		$this->bname  = "";
		$this->extractSection = false;
		$this->section = null;
		$this->attributes = array();
		$this->foundDot = false;
	}

	// Attributes table 'getter/setter' interface.
	// v1.2 change.
	public function setA($key,$value) { $this->attributes[$key]=$value; }
	public function getA($key)		  { return @$this->attributes[$key]; }
	
	public function create( &$title, &$article )
	/*
	 *   Hook function handler.
	 *   Substitutes an ArticleEx object instance in place
	 *   of the usual MW Article class object.
	 *
	 */
	{
		// Check the special cases
		$ns = $title->getNamespace();
		if ($ns == NS_MEDIA || $ns == NS_IMAGE || $ns == NS_CATEGORY)
			return null; // let the normal flow proceed.
		
		parent::__construct( $title );
		$article = $this;

		// Let's check the database if a page matching the title name
		// actually exists... if one does, then abort our special hook.
		if ($this->getID()!=0)
			return true;  // continue the hook chain.
		
		// First, extract base name from MW Title Class object.
		// The base name takes the form:
		//    somemwprefixeddbkey.type
		// Where only the right most '.' delimits the 'type' information
		$n = $this->fname = $this->getTitle()->getPrefixedDBkey();
		$p = strrpos( $n, "." );
		
		if ($p===false) 	// no '.' found, bail out.
			return true;

		// Raise a flag to simplify our lives in the view() method.
		$this->foundDot = true;

		// We are anyhow dealing with a valid MW title name,
		// so spare the security checks.
		$this->bname = substr($n, 0, $p);
		$this->type  = substr($n, $p+1 );

		return true; // continue the hook chain.
	}

	public function view()
	{
		// V1.2 change
		// Don't forget to check user rights (if applicable) 
		// in the extensions that attach here!
		if (!wfRunHooks( 'ArticleViewExBegin', array( &$this ) ))
			return; // v1.7 feature (a)
				
		if (!$this->foundDot)
			return parent::view();				
				
		// if the article does not exists (probably given the 'type' extension
		// appended to the title name) & an actual 'type' extension is 
		// found in the title name, then let's check if an article actually
		// exists with a title name that EXCLUDES the 'type' extension.
		if ($this->getID()!=0)
			return parent::view();
			
		// at this point, we know that the article does not exist.
		// Let's check for an article with for title one without
		// the 'type' extension.
		$title = Title::newFromText($this->bname);
		$this->mTitle = $title; 
		$this->clear();
		$this->oldid= null;

		// nope...
		if ($this->getID()==0)
		{
			// good thing we kept the full title name just in case!
			$title = Title::newFromText($this->fname);
			$this->mTitle = $title;
			$this->clear();
			$this->oldid = null;
			return parent::view(); // go with default behavior
		}
			
		// At this point, we know we have a valid article
		
		// Check User Rights...
		if (!$title->userCanRead()) 
			return parent::view(); // let the normal flow handle this one...

		$this->loadContent();
		
		$this->extractSection = empty($this->type) ? false:true;	

		#echo "type = $this->type, extract= $this->extractSection <br/>";

		if ($this->extractSection)
			$this->section = $this->extractSection( $this->type, $this->mContent ) ;			

		#echo $this->section;

		// DEFAULT BEHAVIOR:
		// If we have detected a "type" in the page name,
		// then serve the page without the skin.
		$this->noskin = empty($this->type) ? false:true;
		$content = $this->section;
		
		// The other parameters can be fetched from the object itself.
		// Parameters passed:
		// - ArticleEx object instance
		// - type e.g. xml, xsl etc.
		// - content  (what is about to be returned if nothing is done)
		// 
		// NOTE: at this point, the variable $content does not include the
		//       Mediawiki content enclosing tags e.g. <xml> content </xml>
		wfRunHooks( 'ArticleViewEx', array( &$this, &$this->type, &$content ) );

		global $wgOut;
		$wgOut->clearHTML();
		$wgOut->addHTML( $content );				
		
		// next, let's see if we need to apply special treatment.
		if ($this->noskin)
			$wgOut->setArticleBodyOnly(true);
			
	}
	private function extractSection( &$type, &$content )
	{
		// check if we have multiple choices
		if (isset(self::$qType[$type]))
		{
			$tags = self::$qType[$type];
			foreach( $tags as $tag )
			{
				$pattern = "/<".$tag."(?:.*)\>(.*)(?:\<.?".$tag.">)/siU";
				$r = preg_match_all( $pattern, $content, $m );
				if ($r>0)
					break;
			}
		}
		else
		{
			$pattern = "/<".$type."(?:.*)\>(.*)(?:\<.?".$type.">)/siU";
			preg_match_all( $pattern, $content, $m );
		}
		
		return trim( $m[1][0] ); // just the first submatch
	}

// =================================================================================

	public function loadPageData( $data = 'fromdb' )
	/*
	 *   This hook is used to retrieve all the categories associated with an article.
	 */
	{
		$ret = parent::loadPageData( $data ); // get return code just in case.
	
		// At this point, we have a valid 'page id' in a local variable.
		// Use it to query the database for 'categorylinks'
		$dbr      =& wfGetDB( DB_SLAVE );
		$page     = $dbr->tableName( 'page' );
        $catlinks = $dbr->tableName( 'categorylinks' ); 

		$id = $this->getID();

		$query = "SELECT cl_to FROM {$catlinks} WHERE {$catlinks}.cl_from={$id}";
		
		$results = $dbr->query( $query );
		$count   = $dbr->numRows( $results );

		if ($count>=1)
			while( $row = $dbr->fetchObject( $results ) )
				$this->categories[] = $row->cl_to;
			
		$dbr->freeResult( $results );
					
		return $ret;
	}

} // End class definition.
?>