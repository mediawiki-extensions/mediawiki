<?php
/*
 * PageAttributes
 * Mediawiki Extensions
 * @author: Jean-Lou Dupont (www.bluecortex.com)
 *
 * COMPATIBILITY:
 * - tested on Mediawiki v1.8.2
 *
 * This extension enables reading, updating & writing
 * attributes associated to Mediawiki pages. A new field
 * 'page_attribute' must be created in the Mediawiki database
 * in the 'page' table.
 *
 * > page_attribute oftype 'tinyblob' 
 *
 * The attributes associated with a page are then available
 * (once the page is loaded through an "article" object)
 * through the global object $paObj.
 *
 * A typical usage pattern:
 *
 * global $paObj;
 * $paObj->setAttribute($pageid, $key, $value );   # change an attribute -- application specific 
 * $paObj->processTable($pageid);                  # prepare for the update database process
 * $paObj->updatePageAttributes($pageid);          # commit changes to the database.
 *
 * HISTORY:
 * v1.1    - Added loadPageAttributes 
 * v1.2    - Added getPageAttributesTable
 *         - Support for both list(comma delimited) & single entry of "values"
 * v1.3    - Added getGlobalObjectName & getGlobalObject static functions
 *         - Changed to auto-processing of attributes
 *           (i.e. ProcessAttributes method called automatically when
 *            attributes are loaded from page).
 */

$wgExtensionCredits['other'][] = array(
    'name'    => "PageAttributes [http://www.bluecortex.com]",
	'version' => '$LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

// global object.
$paObj = new PageAttributes;

$wgHooks['ArticlePageDataBefore'][] = array( $paObj, 'hSetupInterceptPageAttributes' );	
$wgHooks['ArticlePageDataAfter'][]  = array( $paObj, 'hInterceptPageAttributes' );

class PageAttributes
{
	var $pAttr;
	var $pAttrTable;
	
	function PageAttributes() { }

	static function getGlobalObjectName() { return "paObj";           }
	static function &getGlobalObject()    { return $GLOBALS['paObj']; }	

	/*
	 * Called before a page data is loaded from the database.
	*/
	function hSetupInterceptPageAttributes( &$article, &$fields ) { $fields[] = 'page_attributes';	}
	
	/*
	 * Called after the page's data (namespace, title, restrictions etc.)
	 * is loaded from the database.
	 * Store them in a contextual array.
   	*/
	function hInterceptPageAttributes( &$article, &$data )
	{
		# unique $id to store the retrieved data.
		# This is useful as sometimes more than one
		# page can be loaded during a transaction.
		$id = $article->getID();
		
		$this->pAttr[$id] = $data->page_attributes;	
		
		$this->ProcessAttributes( $id );	
	}

	/*
	 * The following get/set pair shouldn't normally be used
	 * by outsiders. Use the get/set attribute interface
	 * and don't forget to 'processAttributes' and 'processTable'.
	*/
	function setPageAttributes( $id, $attr ){ $this->pAttr[$id] = $attr; }
	function getPageAttributes( $id )		{ return $this->pAttr[$id]; }
	function getPageAttributesTable( $id )  { return $this->pAttrTable[$id]; }
	function clearPageAttributes( $id )     
	{
		if (isset($this->pAttr[$id])) 
			unset($this->pAttr[$id]);
		if (isset($this->pAttrTable[$id])) 
			unset($this->pAttrTable[$id]); 
	}
	
	function loadPageAttributes( $id )
	{
		$dbr =& wfGetDB( DB_SLAVE );
		
		$fields = array(
				'page_id',
				'page_attributes' ) ;

		$row = $dbr->selectRow( 'page',
			$fields,
			array('page_id' => $id),
			'PageAttributes::loadPageAttributes' );

		$this->pAttr[$id] = $row->page_attributes;
	}
		
	function updatePageAttributes( $id )
	{
		$dbw =& wfGetDB( DB_MASTER );

		$dbw->update( 'page',
			array( /* SET */
				'page_touched' => $dbw->timestamp(),
				'page_attributes' => $this->pAttr[$id],
			), array( /* WHERE */
				'page_id' => $id
			), 'PageAttributes::UpdatePageAttributes'
		);
	}
	
	function setAttribute( $id, $key, $value ){ $this->pAttrTable[$id][$key] = $value; }
	function getAttribute( $id, $key )		  { return $this->pAttrTable[$id][$key]; }

	/*
	 * Process the raw attribute string returned by the database.
	 * This function "explodes" the string into a more usable form 
	 * i.e. associative array.
	 *
	 * Attribute format:
	 *  key1=value1[|keyn=valuen|]
	*/
	function processAttributes( $id )
	{
		if (empty($this->pAttr[$id]))
			return;
			
		foreach( explode( '|', trim( $this->pAttr[$id] ) ) as $att ) 
		{
			$temp = explode( '=', trim( $att ) );
			$temp2 = explode( ',', trim( $temp[1] ) );
			if (count($temp2)==1)
				$temp2= (string) $temp2[0];
			$this->pAttrTable[$id][$temp[0]] = $temp2;
		}
	}

	/*
	 * This function prepares the attributes table
	 * in a format compatible with the update process.
	*/
	function processTable( $id )
	{
		$bits = array();
		$t = $this->pAttrTable[$id]; # shortcut.
		
		if (empty($t))
			return;
			
		ksort( $t );
		foreach( $t as $key => $value ) 
		{
			if( $value != '' ) 
				$bits[] = "$key=$value";
		}
		$b = implode( '|', $bits );
		
		$this->setPageAttributes($id, $b);
	}
}
?>
