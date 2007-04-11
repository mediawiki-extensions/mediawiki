<?php
/*
 * TitleListTools.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a 'magic word' interface to retrieve,
 *           filter and group title lists.
 *
 * Features:
 * *********
 *
 * {{#gettitles:
 *    
 *
 * {{#numtitles:
 *    
 *
 * {{#firsttitle:
 *    
 *
 * {{#lasttitle:
 *    
 *
 *
 * DEPENDANCIES:
 * 1) 'ExtensionClass' extension
 *
 * Tested Compatibility:  MW 1.8.2, 1.9.3
 *
 * HISTORY:
 * -- Version 1.0:	initial availability
 *          
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'TitleListTools Extension', 
	'version' => '1.0',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

// Let's create a single instance of this class
TitleListTools::singleton();

class TitleListTools extends ExtensionClass
{
	static $mgwords = array('gettitles', 'numtitles','firsttitle','lasttitle' );
	
	public static function &singleton( )
	{ return parent::singleton(); }
	
	// Our class defines magic words: tell it to our helper class.
	public function TitleListTools()
	{	return parent::__construct( self::$mgwords );	}

	// ===============================================================
	var $tlist;

	public function mg_gettitles( &$parser )
	{
		$params = $this->processArgList( func_get_args(), true );
		$this->setupParams( $params );
		#var_dump($params);
		$this->getList( $params['context'], $params['namespace'], $params['categoryfilter'], 
						$params['from'],    $params['order'],     $params['limit']);
		
		return '';
	}
	public function mg_numtitles( &$parser )
	{
		$params = $this->processArgList( func_get_args(), true );
		$this->setupParams( $params );
		
		return (count($this->tlist[$params['context']]));
	}
	public function mg_firsttitle( &$parser )
	{
		$params = $this->processArgList( func_get_args(), true );
		$this->setupParams( $params );
		
		if (empty($this->tlist[$params['context']]) )
			return '';
		
		return ($this->tlist[$params['context']][0] );
	}
	public function mg_lasttitle( &$parser )
	{
		$params = $this->processArgList( func_get_args(), true );
		$this->setupParams( $params );
		
		if (empty($this->tlist[$params['context']]) )
			return '';
		
		$count = count($this->tlist[$params['context']]);
		
		return ($this->tlist[$params['context']][$count-1] );
	}

	// ===============================================================
	
	private function getList( &$context, &$namespace, &$catfilter, &$fromtitle, $order, $limit )
	{
		$dbr      =& wfGetDB( DB_SLAVE );
		$page     = $dbr->tableName( 'page' );
		$catlinks = $dbr->tableName( 'categorylinks' );

		#var_dump( $fromtitle );
		#echo "from= $fromtitle \n";
		#echo "namespace= $namespace \n";
		#echo "catfilter= $catfilter \n";
		#echo "context= $context \n";
		#var_dump( $context );

		$catlinks  = empty($catfilter)   ? ""         : ", {$catlinks}";
		$namespace = is_int( $namespace) ? $namespace : Namespace::getCanonicalIndex( strtolower( $namespace ) ); 
        $filter    = empty($catfilter)   ? ""         : "AND (cat.cl_to = '$catfilter')";
		$from      = empty($fromtitle)   ? ""         : "AND (STRCMP(page.page_title,'$fromtitle')=1)";

		#echo "filter= $filter \n";
		#echo Namespace::getCanonicalIndex( $namespace );
		
		switch($order)
		{
			case 'category':
				$order = "ORDER BY cat.cl_to";
				break;
			case 'title_asc':
				$order =  "ORDER BY page.page_title";
				break;	
			case 'title_desc':
				$order =  "ORDER BY page.page_title DESC";
				break;
			default:
				$order = '';
				break;	
		}

$query = 
<<<EOT
		SELECT page_title, page_id
		FROM  (SELECT page_title, page_id
		       FROM  `page` AS page, `categorylinks` AS cat
		       WHERE (page.page_namespace = {$namespace} ) 
			   		AND (page.page_id = cat.cl_from)
					$from
					$filter
			   ORDER BY page.page_title
		       LIMIT {$limit} ) 
			   					AS page, `categorylinks` AS cat
		WHERE (page.page_id = cat.cl_from)
		{$order}
		LIMIT {$limit}	
EOT;

		#echo "query= $query \n";

		$results = $dbr->query( $query );
		$count   = $dbr->numRows( $results );

		$namespace = Namespace::getCanonicalName( $namespace );

		if ($count>=1)
			while( $row = $dbr->fetchObject( $results ) )
				$this->tlist[$context][] = "{$namespace}".':'.$row->page_title;

		$dbr->freeResult( $results );
		#var_dump($this->tlist[$context]);
	}

	// ===============================================================
	private function setupParams( &$params )
	{
		$this->getCurrentPage( $ns, $title );

		#var_dump($params);

		$template = array(
			array( 'key' => 'context',        'index' => '0', 'default' => "context0" ),
			array( 'key' => 'namespace',      'index' => '1', 'default' => "$ns" ),			
			array( 'key' => 'categoryfilter', 'index' => '2', 'default' => '' ),
			array( 'key' => 'from',           'index' => '3', 'default' => '' ),
			array( 'key' => 'grouping',       'index' => '4', 'default' => '' ),
			array( 'key' => 'order',          'index' => '5', 'default' => "category" ),
			array( 'key' => 'limit',          'index' => '6', 'default' => "200" ),
			#array( 'key' => '', 'index' => '', 'default' => '' ),
		);
		parent::initParams( $params, $template );
		#var_dump($params);
		$this->cleanUpParams( $params );
	}
	private function cleanUpParams( &$params )
	{
		// clean up parameters to avoid SQL Injection related attacks.
		foreach( $params as $index => &$el )
		{
			if (empty($el[$index])) continue;  // fix bug in PHP 5.2.1
			$el[$index] = trim(str_replace( array( ';',"'",'"','`' ), 
										'_', $el[$index] ));
		}
	}
	public function getCurrentPage( &$ns, &$title )
	{
		global $wgTitle;
		$ns_num = $wgTitle->getNamespace();
		$ns     = Namespace::getCanonicalName( $ns_num );
		$title  = $wgTitle->getDBkey();
		
		return $ns.":".$title;
	}

} // end class	
?>