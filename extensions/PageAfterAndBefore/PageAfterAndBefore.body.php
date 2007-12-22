<?php
/**
 * @author Jean-Lou Dupont
 * @package PageAfterAndBefore
 * @version @@package-version@@
 * @Id $Id$
*/
//<source lang=php>
class PageAfterAndBefore
{
	const thisName = 'PageAfterAndBefore';
	const thisType = 'other';
	
	// Our class defines magic words: tell it to our helper class.
	public function __construct() {}

	// ===============================================================
	var $cList = array();

	public function mg_pagebefore( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		$this->setupParams($params);

		$res = $this->getPages( $params['namespace'], $params['title'], 'desc',$params['category'] );
		if (!isset($res[0]))
			return null;
		return $res[0];
	}
	public function mg_pageafter( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		$this->setupParams($params);

		$res = $this->getPages( $params['namespace'], $params['title'], 'asc',$params['category'] );		
		if (!isset($res[0]))
			return null;
		return $res[0];
	}
	public function mg_firstpage( &$parser )
	// If 'namespace' is not supplied, defaults to current page's namespace
	{
		$params = StubManager::processArgList( func_get_args(), true );
		$this->setupParams($params);
		
		$res = $this->getPages( $params['namespace'], '' , 'asc', $params['category'] );

		if (!isset($res[0]))
			return null;

		// filter out if requested and currentpage==firstpage
		$currentpage = $this->getCurrentPage( $ns, $title );
		if ( ($this->filterCurrent( $params )) && ( $res[0]== $currentpage))
			return '';
			
		return $res[0];
	}
	public function mg_lastpage( &$parser )
	// If 'namespace' is not supplied, defaults to current page's namespace
	{
		$params = StubManager::processArgList( func_get_args(), true );
		$this->setupParams($params);

		$res = $this->getPages( $params['namespace'], '' , 'desc', $params['category'] );		

		if (!isset($res[0]))
			return null;

		// filter out if requested and currentpage==lastpage
		$currentpage = $this->getCurrentPage( $ns, $title );
		if ( ($this->filterCurrent( $params )) && ( $res[0]== $currentpage))
			return '';

		return $res[0];
	}
	/**
	 * Verifies the 'filtercurrent' parameter
	 */
	protected function filterCurrent( &$params )
	{
		// true by default.
		if ( !isset( $params['filtercurrent'] ))
			return true;
			
		$f = strtolower( $params['filtercurrent'] );
		return ( ($f=='y') || ($f=='yes') || ($f=='1')) ? true:false;
	}
	private function setupParams( &$params )
	{
		$this->getCurrentPage( $d_ns_name, $d_title );

		$template = array(
			array( 'key' => 'context',       'index' => '0', 'default' => 'context0' ),
			array( 'key' => 'namespace',     'index' => '1', 'default' => "{$d_ns_name}" ),
			array( 'key' => 'title',         'index' => '2', 'default' => "{$d_title}" ),
			array( 'key' => 'category',      'index' => '3', 'default' => '' ),
			array( 'key' => 'filtercurrent', 'index' => '4', 'default' => 'yes' ),
			#array( 'key' => '', 'index' => '', 'default' => '' ),
		);
		StubManager::initParams( $params, $template );
	}
	public function getCurrentPage( &$ns, &$title )
	{
		global $wgTitle;
		$ns_num = $wgTitle->getNamespace();
		if ($ns_num !== NS_MAIN)
			$ns = Namespace::getCanonicalName( $ns_num );
		else
			$ns = '';
		$title  = $wgTitle->getText();
		
		return $ns.":".$title;
	}
	public function getPages( $namespace, $titlename, $dir='asc', $category = null, $limit=2 )
	{
		$orderDir = ($dir=="asc")      ? "ASC" : "DESC";
		$cmpDir   = ($orderDir=='ASC') ? "1"   : "-1";
		$where = "";
		$cat = null;
		$pages = array();
						
		$dbr      =& wfGetDB( DB_SLAVE );
		$page     = $dbr->tableName( 'page' );
        $catlinks = $dbr->tableName( 'categorylinks' ); 

		if (!empty($titlename))
		{
			if (!empty($namespace))
				$namespace.=':';

			$title   =  Title::newFromText( $namespace.$titlename );
			if (!is_object($title))
				return null;
				
			$ns = $title->getNamespace();
			$unescaped_key = $title->getDBkey();
			
			// fix for apostrophes in title generating database access error
			$key = $dbr->strencode( $unescaped_key );
			
			if ($ns !== NS_MAIN)
				$namespace = Namespace::getCanonicalName( $ns );
			else 
				$namespace ='';
				
			$where = "AND STRCMP({$page}.page_title,".'"'.$key.'"'."={$cmpDir}";
		}
		else
		{
			if (!empty($namespace))
				$ns = Namespace::getCanonicalIndex( strtolower( $namespace ) );
			else
				$ns = NS_MAIN;
		}
		// If a category is specified.
		if (!empty($category))
		{
			// fix for apostrophes in title generating database access error			
			$category = $dbr->strencode( $category );
			
			$where .= " AND {$catlinks}.cl_to = ".'"'.$category.'"'." AND {$catlinks}.cl_from = page_id";
			$cat = ", {$catlinks}";
		}
				
		$query = "SELECT page_namespace, page_title, page_id FROM {$page} {$cat} WHERE {$page}.page_namespace = {$ns} {$where} ORDER BY {$page}.page_title {$orderDir} LIMIT {$limit}";
		$results = $dbr->query( $query );
		$count   = $dbr->numRows( $results );
		
		if ($ns !== NS_MAIN)
			$namespace = Namespace::getCanonicalName( $ns );
		else 
			$namespace ='';
		
		if (!empty( $namespace ))
			$namespace .= ':';
		
		if ($count>=1)
			while( $row = $dbr->fetchObject( $results ) )
				$pages[] = $namespace.$row->page_title;

		$dbr->freeResult( $results );
		return $pages;
	}

} // end class	
//</source>