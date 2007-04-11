<?php
/*
 * FilterTitles WikiMedia extension
 * @author Jean-Lou Dupont
 * @package MediaWiki
 * @subpackage Extensions
 * @licence GNU General Public Licence 2.0
 *
 * This extension provides useful tools
 * for implementing blog functionality to a WEB site.
 *
 * {{:ftl: context, limit, namespace, order, author, sub-page}}
 * Creates a filtered list of a maximum of LIMIT entries from the
 * NAMESPACE and AUTHOR, ORDERs [NEW or UPDATED] the entries and
 * skip the SUB-PAGES if instructed to.
 * The list is stored in CONTEXT.
 *
 * {{ftg:context, index}}
 * Returns the entry INDEX from CONTEXT. The return value is an article title.
 *
 * {{fte:context, prepend, postpend, include wikitext link, add namespace, add alternate text, postpend2, include wikitext template}}
 * Enumerates one by one the entries in list CONTEXT whilst:
 * - optionally prepending some wikitext
 * - optionally postpending some wikitext
 * - optionally adding a namespace reference (namespace:title)
 * - optionally adding the {{ }} templating function
 * - optionally addint the [[ ]] linking function
 * - optionally adding alternate text to the wikitext link [[namespace:title|altext]]
 * - optionally adding some text to the title e.g. [[namespace:title/summary]] 
 *
 * {{ftn:context}}
 * Returns the number of entries in the list associated with CONTEXT.
 *
 * Add to LocalSettings.php
 * with: include("extensions/filterTitles.php");
 *
 * TESTED on Mediawiki v1.8.2
 *
 * HISTORY:
 *  v1.0 initial version
 *  v1.1 included contextual lists
 *  v1.2 included parameter to skip sub-pages from the title list
 *  v1.3 - added verification of 'read' right for integration with
 *       'AuthorRestriction' extension.
 *       - added 'read restriction' support.
 */

# Limit the number of titles that can be processed.
global $ftLowerLimit, $ftUpperLimit;
define('ftUpperLimit', 100);
define('ftLowerLimit',1);

$filterTitlesVersion = "(v1.3)";

$wgExtensionCredits['other'][] = array(
    'name' => "FilterTitles $filterTitlesVersion [http://www.bluecortex.com]",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);
$wgExtensionFunctions[] = "wfSetupFilterTitles";

$wgHooks['LanguageGetMagic'][] = 'wfFilterTitlesGetMagic';

function wfFilterTitlesGetMagic( &$magicWords, $langCode ) 
{
	$magicWords['ftl'] = array( 0, 'ftl' ); # populate list
	$magicWords['ftg'] = array( 0, 'ftg' ); # get list entry
	$magicWords['ftn'] = array( 0, 'ftn' ); # get count of entries in list
	$magicWords['fte'] = array( 0, 'fte' ); # return the list of entries
	$magicWords['ftr'] = array( 0, 'ftr' ); # return the list of entries

	return true;
}

class FilterTitlesFunctions
{
	
	# this will contain the title list 
	# the list is populated by calling ftl in an article
	var $titleList=array();
	
	# Enumerate the list of titles
	# ----------------------------
	# this function enumerates the content of titleList.
	# Each entry gets prepended with $pre
	# and postpended with $post.
	# - If $il==true, the wikitext '[[' is pre-pended
	# and the wikitext ']]' is post-pended.
	# - If $il2==true, the wikitext '{{' is pre-pended
	# and the wikitext '}}' is post-penped.
	# 
	function fte(&$parser,$context=0,$pre1="",$post1="",$il=FALSE,$nspace,$altext=FALSE,$post2='',$il2=FALSE)
	{
		if ($nspace!=="")
			$nspace.=":";
			
		$parser->disableCache();
		
		$out="";
		$index=0;
		
		while($this->titleList[$context][$index]['title'] <>"")
		{
			$alt=$altext==true ? "|".$this->titleList[$context][$index]['title']:"";
			$out.=$pre1.($il?"[[":"").($il2?"{{":"").$nspace.$this->titleList[$context][$index++]['title'].$post2.$alt.($il2?"}}":"").($il?"]]":"").$post1;
		}
			
		return $out;
	}
		
	function ftl( &$parser, $context=0, $limit, $n=-1, $o='', $author='',$isub=FALSE)
    {
    	$n= $this->setNamespace($n);
		$nsf = $this->getNsFragment($n);
		
		$limit=($limit > ftUpperLimit) ? ftUpperLimit : $limit;
		
    	$orderField='';
		$authorWHERE='';
		
        $parser->disableCache();

		$dbr =& wfGetDB( DB_SLAVE );
		$page = $dbr->tableName( 'page' );
		$rev = $dbr->tableName( 'revision' );
		if($author != '') {
			$author = str_replace("'", "''", $author);	// escape single quotes
			$authorWHERE = " AND $rev.rev_user_text = '{$author}'";
		}
			
		if ($o == 'new') 
			$orderField = "page_id";
		else 
			$orderField = "rev_timestamp";
				
		$querySQL = "SELECT page_namespace, page_restrictions, page_title, page_id, page_latest FROM {$page},{$rev} WHERE {$page}.page_latest = {$rev}.rev_id AND {$nsf} {$authorWHERE}	ORDER BY {$orderField} DESC LIMIT 0,{$limit}";
		$res = $dbr->query( $querySQL );

		$count = $dbr->numRows( $res );
		if( $count > 0 ) 
		{
			# Make list
			$index=0;
			while( $row = $dbr->fetchObject( $res ) )
			{
				$titre=$row->page_title;
				
				# assume we will be including the title in the list
				$inc_t=true;
				
				# V1.3 feature: 'read' right enforcement
				# Create 'stub' title to gain access to useful methods
				$tt = Title::makeTitle($row->page_namespace, $row->page_title);
				
				// Can the user 'read' the title?
				if ( !$tt->userCanRead() )
					continue;
					
				// Get 'read' restrictions
				$read_restrictions = $tt->getRestrictions('read');
					
				# First, strip off sub-page entries from the list if we are asked to
				if ($isub)
				{
					# check if the title is a sub-page
					if (strpos($titre,"/")===false)
						$inc_t=true;
					else 
						$inc_t=false;
				}
				if ($inc_t)
				{	
					$this->titleList[$context][$index]['title'] = $titre;
					$this->titleList[$context][$index++]['read-restrictions'] = $read_restrictions;
				}
			}
		}
		$dbr->freeResult( $res );

		# we do not need to return anything at this point.
		# use "ftg" to 'get' a title name
		return '';
    }
	
	function getNsFragment($n) 
	{
		$ns = (int)$n;
		return $ns > -1 ? "page_namespace = {$ns}" : "page_namespace != 8";
	}

	function setNamespace( $nst ) 
	{
		global $wgContLang;
		$nsi = $wgContLang->getNsIndex( $nst );
		if( $nsi !== false )
			return $nsi;
		if( $nst == '-' )
			return NS_MAIN;
	}
	
	
    function ftg( &$parser,$context=0,$index )
    {
    	# returns the title of an article 
		# contained in the titleList array 
	
		if ($index>count($this->titleList[$context]))
			return '';
	
        return ($this->titleList[$context][$index]['title']);
    }

    function ftn( &$parser,$context=0 )
    {
    	# returns the number of titles currently held in the titleList array 
        return count($this->titleList[$context]);
    }

    function ftr( &$parser,$context=0 , $index)
    {
    	# returns the number of titles currently held in the titleList array 
		# 
        return $this->titleList[$context][$index]['read-restrictions'];
    }
}

function wfSetupFilterTitles()
{
	global $wgParser, $wgFilterTitlesFunctions, $wgHooks;

	$wgFilterTitlesFunctions = new FilterTitlesFunctions;

	$wgParser->setFunctionHook( 'ftl', array( &$wgFilterTitlesFunctions, 'ftl' ) );
	$wgParser->setFunctionHook( 'ftg', array( &$wgFilterTitlesFunctions, 'ftg' ) );
	$wgParser->setFunctionHook( 'ftn', array( &$wgFilterTitlesFunctions, 'ftn' ) );
	$wgParser->setFunctionHook( 'fte', array( &$wgFilterTitlesFunctions, 'fte' ) );
	$wgParser->setFunctionHook( 'ftr', array( &$wgFilterTitlesFunctions, 'ftr' ) );
}
?>