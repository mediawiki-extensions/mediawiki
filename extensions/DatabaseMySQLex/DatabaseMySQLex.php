<?php
/*
 * DatabaseMysqlex.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * IMPLEMENTATIONS NOTES:
   
	Article 'pageData' --> uses 'selectRow' && namespace translation required
	Article 'insertOn' --> uses 'insert' &&  namespace translation required
	Article 'updateRedirectOn' --> uses 'replace' && namespace translation required
  	Article::delete --> uses 'selectField' && namespace translation required
	Article::getLastNAuthors --> uses 'select', 'fetchObject' && namespace translation required
  	Article::doDeleteArticle --> uses 'insertSelect' for archiving && namespace translation required
	Article::doDeleteArticle --> uses 'delete' for updating recentchanges && namespace translation required
	Article::info --> 'selectField' && n t r
	Article::getUsedTemplates --> 'select', 'fetchObject'  && n t r
   Article Delete:
   
   
   Article Update:

	LinksUpdate::queueRecursiveJobs --> 'select', 'fetchObject' && n t r
	LinksUpdate::invalidatePages -->  'select', 'fetchObject', 'update' && n t r
	LinksUpdate::getLinkInsertions --> 
	LinksUpdate::incrTableUpdate --> 'delete', 'insert' && n t r
	LinksUpdate::getExistingLinks --> 'fetchObj' && n t r 
	LinksUpdate::getExistingTemplates
	
	
	LogPage::saveContent --> 'insert' && n t r

	RecentChange::save --> uses 'insert'  && namespace translation required
	RecentChange::notifyEdit --> uses RecentChange::save
	RecentChange::notifyNew  --> uses RecentChange::save
	RecentChange::notifyMove --> uses RecentChange::save
	RecentChange::notifyLog  --> uses RecentChange::save
	RecentChange::loadFromCurRow --> static function used ...

	Revision 'fetchFromConds' --> uses 'select' && namespace translation required
	
	
	Revision 'newFromTitle' --> namespace translation required
	Revision 'loadFromTitle'--> namespace translation required
	Revision 'loadFromTimestamp'--> namespace translation required
	Revision 'fetchAllRevisions'--> namespace translation required
	Revision 'fetchRevision'--> namespace translation required
	
	Revision 'getTitle': uses 'selectRow' and namespace translation required 

	Title::pageCond --> namespace dependancy
	Title::invalidateCache --> 'update' && n t r

 * TODO
 * ====
 * 1) add protection against use with unsupported MW version
 * 
 * History:
 * ========
 * v1.0
 */

class DatabaseMySQLex extends DatabaseMysql
{
	const thisName = 'DatabaseMySQLex';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	public function __construct( $server, $user, $password, $dbName, $failFunction, $flags )
	{ 
		global $wgExtensionCredits;
		
		$wgExtensionCredits['other'][] = array(
		    'name'        => self::thisName,
			'version'     => '$LastChangedRevision$',
			'author'      => 'Jean-Lou Dupont [http://www.bluecortex.com]',
			'description' => 'Extends the standard DatabaseMysql class. '
		);

		return parent::__construct( $server, $user, $password, $dbName, $failFunction, $flags );			
	}
###################################################################################
/*
    New Methods
*/
###################################################################################
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits, $wgDBclass, $wgDBtype;
	
		if (!isset( $wgDBclass)) return;
			
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if ($el['name']==self::thisName)
				$el['description'].=" \$wgDBtype is set to <b>{$wgDBtype}</b> and \$wgDBclass is set to a <b>{$wgDBclass}</b>.";	
	}

###################################################################################
/*
    Overloaded Methods
*/
###################################################################################

	function makeList( $a, $mode = LIST_COMMA ) 
	{
		#var_dump( $a );
		#echo "<br/>";
		
		// Check for 'page_namespace' condition
		
			
		return parent::makeList( $a, $mode );
	}

} // end class definition.
?>