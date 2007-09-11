<?php
/*
 * ArtifactClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Usage:
 *
 * <artifact id="namespace:page" [key=value] [...] />
 *
 *  where:
 *        - "id" corresponds to the page containing the artifact
 *        - "list" corresponds to a context list [optional] 
 *        - "key" corresponds to a tagged parameter in the referenced
 *          artifact. [optional]  
 *        - "value" serves as value to store against "key"
 *
 * Dependancies:
 * =============
 *
 * - ArticleCacheClass
 * - TagClass
 *
 * Page structure:
 * ===============
 *
 * Page Heading
 *  Table Heading
 *   Column Heading
 *    Group Heading
 *     Row 
 *      Subrow x
 *      Subrow x+1
 *      Subrow ...
 *      Subrow n
 *
*/
class ArtifactClass
{
	var $cache;
	var $parser;
	var $parserOptions;
	var $tagObj;
	// ----------------
	var $view;
	var $columns;
	var $subrows;
	var $subrowsVars;
	var $coltypes;
	var $vectors;
	var $vectorVars;
	var $pages;
	var $columntitles;
	var $table;
	// +++++++++++++++++++++++
	// vars for view rendering
	var $groupingDone;	
	var $columnIndex;
	var $subrowCount;
	var $columnsCount;
	var $rowCount;
	var $pageCount;
	var $linesPage;
	var $lineCount;
	var $tableBorder;
	var $tableHeadingRepeat;
	
	// reserved tags.
	// Tags from MW are always lowercase. If we use some
	// uppercase in the tag name, we won't get collisions.
	static $rTags = array( "bGroup", "blankLine", "bPage" );
	
	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	static function getGlobalObjectName() { return "artifactObj";           }
	static function &getGlobalObject()    { return $GLOBALS['artifactObj']; }	
	// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function ArtifactClass( )
	{ 
		$this->cache  = ArticleCacheClass::getGlobalObject();
		$this->parser = new Parser;
		$this->parserOptions = new ParserOptions;
		
		$this->viewReady = false;
		
		// we don't need all the fancy functions of the parser.
		$this->parser->mFirstCall = false;
		
		// Attach our TagClass instance to our Parser.
		$this->tagObj = new TagClass( $this->parser );
		
		// some defaults.
		$this->linesPage = 80;
	}
	public function init( &$wgParser )
	{
		$wgParser->setHook(          "artifact",      array($this, 'set' )  );
		$wgParser->setHook(          "artifactlist",  array($this, 'dolist' ) );	
		$wgParser->setFunctionHook(  "artifactlist",  array($this, 'mgList') );
	}

	public function set( $text, $argv, &$parser )
	{
		// Retrieve artifact id used for loading the wiki page.
		// If the id turns out to be empty, bail out.
		$id = $argv['id'];
		if (empty($id))
			return "Artifact: missing id parameter<br/>";
		else
			unset( $argv['id'] );

		// If the the page in question contains no
		// data, bail out. 
		$content = $this->cache->getArticleContent( $id );
		if (empty($content))
			return null;
			
		// Build ourself a Title Object in order to interface
		// properly with the Parser object.
		global $mediaWiki;
		
		$title = Title::newFromText( $id );
		$id = ArticleCacheClass::makeTitleName($title);
			
		// At this point, the page contains at least some data.
		// Parse it and we'll get all the tagged data in the
		// global TagClass object.			
		$po = $this->parser->parse( $content, $title, $this->parserOptions );
		
		// The $po object (ParserOutput) gets us the categories 
		// attached to the processed page.
		// For earch category, enter it in the tag array.
		$cats = $po->getCategories();
		$index = 0;
		foreach($cats as $c => $v)
		{
			$this->tagObj->set( $id, "category${index}", $c);
			$index++;
		}
		
		// Now our artifact container page should be parsed correctly,
		// replace parameters.
		foreach($argv as $key => $value)
			$this->tagObj->set( $id, $key, $value);
			
		// if we have made modifications to the list,
		// then the view can't possibly be OK for sure.
		$this->viewReady = false;
	}

	/*
	 * Currently, this function is only useful for tidying up
	 * the output when just viewing the page.
	 * The function removes the '\n' newline special characters
	 * before asking the parser to do its job.
	 */
	public function dolist( $input, $argv, &$parser )
	{
		$input = preg_replace("/\n/","", $input);
		$output=$parser->recursiveTagParse($input);
		return $output;
	}
	
	/*
	 *  {{#artifactlist:action= | [parameters] }}
	 *
	 *  Actions supported:
	 *  ==================
	 *  - group : for sorting and grouping artifacts
	 *  - view  : for outputting the artifact list 
	 *
	 */
	public function mgList( /* variable parameter list*/ )
	{
		$args = func_get_args();
		array_shift( $args );  // get rid of parser parameter.
		
		$alist = array();
		// first, process the arguments array
		foreach( $args as $a )
		{
			// all parameters are expected to be
			// key:value pairs
			$t = explode("=",$a);
			$alist[ $t[0] ] = $t[1];	
		}
		
		$result = null;
		$action = $alist['action'];
		// get rid of the "action=xyz" parameter
		if (!empty($action))
			unset( $alist['action'] );
		
		$a = "do$action";
		$actionlist = array (	"test", "view", "group", 
								"columns", "coltypes", "columntitles",
								"subrows", "subrowstyle", 
								"vector", "vectorfnc", 
								"page",	 "table" );
		if (in_array( $action, $actionlist))
			$result = $this->$a( $alist );
		else
			$result = "ArtifactClass: wrong action requested";

		return $result;
	}
	
// ======================================================================================================================
	
	private function doview( &$args )
	{
		if ( !$this->groupingDone )
			$this->dogroup( $args );	
			
		$this->prepareView();			
			
		$result = null;

		var_dump($this->view);
		
		// for each element in the list,
		// 	
		foreach ($this->view as $el )
			$result.= $el['name']."<br/>";
			
		return $result;
	}
	
	/* Example of "view" variable.
	   some additional tags can creep up:
	   "bGroup", "bPage"
	   ---------------------------
		array(2) {
		  [0]=>
		  array(5) {
		    ["name"]=>
		    string(19) "Artifact 1 name tag"
		    ["description"]=>
		    string(26) "Artifact 1 description tag"
		    ["category0"]=>
		    string(5) "Cat_1"
		    ["category1"]=>
		    string(6) "Cat_1a"
		    ["performance"]=>
		    string(2) "99"
		  }
		  [1]=>
		  array(5) {
		    ["name"]=>
		    string(19) "Artifact 2 name tag"
		    ["description"]=>
		    string(26) "Artifact 2 description tag"
		    ["category0"]=>
		    string(5) "Cat_2"
		    ["category1"]=>
		    string(6) "Cat_2a"
		    ["obsolete"]=>
		    string(3) "yes"
		  }
		}	
	*/
	private function dogroup( &$args )
	{
		$this->view = null;
		// The default behavior now is to group
		// the list items by the first 3 category fields
		// in alphabetical order.
		// I.e. key = "$cat0"."$cat1"."$cat2"
		
		// First pass
		$l1 = $this->tagObj->pageTags;
		foreach($l1 as $el => $obj)
		{
			// prepare key.
			$cat0 = $obj['category0'];
			$cat1 = $obj['category1'];
			$cat2 = $obj['category2'];
			$key = "$cat0$cat1$cat2";						
			
			$l2[] = array( $key, $obj );
		}
		ksort( $l2 );
		
		// go through the list and update the
		// "bGroup" entry. This flag will greatly
		// help the rendering process.
		// The "bGroup" flag denotes the beginning
		// of a group of artifacts.
		$pv = null;
		foreach( $l2 as &$el)
		{
			if ( $el[0] != $pv )
				$el[1]['bGroup'] = true;
				
			$pv = $el[0];
		}

		// get rid of the keys.
		foreach ($l2 as $e)
			$l3[] = $e[1];
			
		$this->view = $l3;
	
		#var_dump($this->view);
	
		// we have applied group formatting to the
		// artifact list. Make sure we indicate that.
		$this->groupingDone = true;
	}
	private function prepareView( )
	{
		// if we've got here, that means we need to go through
		// the unformatted list and prepare it for viewing.
		// NOTE:  grouping process must have already taken
		// *****  place at this point. Hence, the internal
		//        variable "view" must be available. 

		// Calculate the number of subrows
		$this->subrowCount = count($this->subrows);
		
		// Calculate the number of rows.
		$this->rowCount = $this->tagObj->countAll();
		
		// Calculate the number of columns
		$this->columnsCount = count($this->columns);		

		// Calculate the number of pages
		// This is tricky has each row is sort of unique:
		// 1) some rows have different subrow characteristics
		// e.g. all empty fields in a subrow --> skip subrow
		//
		// 2) also, if the row starts a new group section,
		//    then a group heading row must be inserted.
		//

		$l = &$this->view;  #shortcut
		$pCount = 0;
		$currentPage = 0;
		// go through each row.
		foreach ($l as $rowI => &$rowEl)
		{
			$rowLineCount = $this->getSubrowFullCount( $rowI );
			
			// is this the beginning of a new group ?
			if ($this->isRowBeginningGroup($rowI))
				$rowLineCount++;
			
			// have we reached the maximum capacity of a page?
			$currentPage+=$rowLineCount;
			
			// Not fancy enough: FIXME
			if ($currentPage >= $this->linesPage) 
			{	
				$pCount++; 
				$currentPage=0;
				
				// indicate page break. 
				$rowEl['bPage'] = true;
			}
		} // end foreach
		
		$this->pageCount = $pCount;	
	}
	
	/*
	 *  {{#artifactlist:action=columns| [tag] | ... }}
	 */
	private function docolumns( $args )	
	{	
		$this->columns = $args;
		$this->updateColumnIndex();	
	}

	/*
	 *  {{#artifactlist:action=coltype| [tag=type] | ... }}
	 */
	private function docoltypes( $args ) {	$this->coltypes = $args;	}

	/*
	 *  {{#artifactlist:action=columntitles| tag = title }}
	 */		
	private function docolumntitles( $args )	
	{
		// just store the whole argument for now.	
		$this->columntitles = $args;
		$this->updateColumnIndex();	
	}

	/*
	 *  {{#artifactlist:action=subrows| row=x | coly=z | ... }}
	 *  where:
	 *    coly can be "col0", "col1" etc.
	 */
	 /* Example of "subrows" variable
	    -----------------------------
		array(2) {
		  [0]=>                     # subrow 0
		  array(4) {
		    [0]=>
		    string(4) "name"
		    [1]=>
		    string(11) "description"
		    [2]=>
		    string(3) "ABC"
		    [3]=>
		    string(3) "XYZ"
		  }
		  [1]=>                     # subrow 1
		  array(2) {
		    [2]=>
		    string(4) "name"
		    [3]=>
		    string(11) "performance"
		  }
		}
	 */
	private function dosubrows( $args )	
	{
		// NOTE: if the user wants a blank line
		//       between rows, use "coly=blankline"
		//       where "y" is the subrow where the
		//       blankline should appear.
		
		// first parameter should be "subrow=x"
		$n = $args['subrow'];
		
		//                colx =  tag
		foreach( $args as $key => $value )
		{
			$r = preg_match("/^col(.*)$/", $key, $m);
			if (!$r)
				continue;
			$col = $m[1];
			$sub = $this->subrows[$n];
			$sub[$col] = $value;
			$this->subrows[$n] = $sub;			
		} 
	}

	/*
	 *  {{#artifactlist:action=subrowstyle| subrow=x | style=(italic, bold, normal) }}
	 */
	private function dosubrowstyle( $args )
	{
		$i = $args['subrow'];
		$s = $args['style'];
		
		$this->setSubrowVars( $i, 'style', $s );	
	}
	
	/*
	 *  {{#artifactlist:action=vector| vector=x | tag=... }}
	 *  e.g. {{#artifactlist:action=vector| vector=1 | tag=name }}
	 *  vector 1 will be based on the "name" tag.
	 *
	 */
	private function dovector( $args )	
	{	
		$this->vectors[ $args['vector'] ] = $args['tag'];	
	}

	/*
	 *  Purpose:   Defines which function should be used for vector "x".
	 *  Functions: 
	 *             1) ifeqshow
	 *    
	 *
	 *  {{#artifactlist:action=vectorfnc| vector=x | fnc=... | params=(x,y,z,...) }}
	 *  e.g. {{#artifactlist:action=vectorfnc| vector=0 | fnc=ifeqshow | params=value }}
	 *  -> for each row, if the tag's value for vector=0 equals 'value' then
	 *     show 'value' in the row in question.
	 *
	 */
	private function dovectorfnc( $args )	
	{	
		$v = $args['vector'];
		$f = $args['fnc'];
		$p = $args['params'];
		unset($this->vectorVars[$v]);
		$this->vectorVars[$v][] = array('fnc' => $f);
		$this->vectorVars[$v][] = array('params' => $p);
	}

		
	/*
	 *  {{#artifactlist:action=page| lines=x }}
	 *
	 *  Page related configuration:
	 *  lines : number of lines per page.
	 *
	 */		
	private function dopage( $args )	
	{
		// store the whole argument array in case.	
		$this->pages = $args;
		
		$this->linesPage = $args['lines'];
	}

	/*
	 *  {{#artifactlist:action=table | border=x }}
	 *  where:
	 *   - border = 0 or 1   (false or true) 
	 *   - heading = eachpage 
	 */
	private function dotable( $args )	
	{	
		$this->table = $args;
		$this->tableBorder = $args['border'];
		$this->tableHeadingRepeat = ( $args['heading'] == 'eachpage' ? true:false);	
	}

	// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
	/*
	 *   ( 0 => (tag,tag_title), 1=> (tag,tag_title) ... )
	 * 
	 */
	private function updateColumnIndex()
	{
		unset($this->columnIndex);
		
		$a = array_keys( $this->columns );
		foreach($a as $index => $key)
			$this->columnIndex[] = array( $a[$index], $this->columntitles[$key] );
	}
	

	private function getColumnTitle( $col )		{ return $this->columnIndex[$col]; 		}
	private function &getRow( $rowI )			{ return $this->view[ $rowI ];			}
	private function &getSubrow( $subI )		{ return $this->subrows[ $subI ]; 		}
	private function getSubrowCount()			{ return count($this->subrows); 		}
	private function isRowBeginningGroup($rowI) { return $this->view[$rowI]['bGroup'];	}
	
	private function setSubrowVar( $i,$k, $v)	{ $this->subrowsVars[ $i ][$k] = $v; 	} 
	private function getSubrowVar( $i,$k )		{ return $this->subrowsVars[$i][$k];	}	
	
	/*
	 * Get the number of subrows filled for a given row.
	*/
	private function getSubrowFullCount( $rowI )
	{
		$count = 0;
		$r = $this->getRow( $rowI );
		$sr= &$this->subrows;
		
		foreach($sr as $index => $el)
			if (!$this->isSubrowEmpty($rowI, $index))
				$count++;
				 
		return $count;		
	}
	
	private function isSubrowEmpty( $rowI, $subrowI )
	{
		// assume the subrow is empty.
		$result = true;	
		
		$r  = $this->getRow( $rowI );
		$sr = $this->getSubrow( $subrowI );
		foreach($sr as $index => $tag)
			if (!empty( $r[$tag] ))
			{
				$result = false;
				break;
			}
		return $result;
	} 
	
	private function dotest($args)
	{
		#var_dump( $this->columntitles );
		#echo "arg = ${args['col']} \n";
		#echo $this->getColumnTitle[ $args['col'] ];
		#var_dump($this->columnsIndex);	
	} 
} // End class definition.
?>