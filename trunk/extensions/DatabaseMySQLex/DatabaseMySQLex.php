<?php
/*
 * DatabaseMysqlex.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * DEPENDANCY:
 * ===========
 * - ExtensionClass (>v1.6)
 *
=======
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
>>>>>>> .r118
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
 *
 *
 * TODO:
 * =====
 * - Interface for setting the translation table
 * - Fix 'query' for translation
 * - Fix 'makeList' for translation 
 *
 */

class DatabaseMySQLex extends DatabaseMysql
{
	const thisName = 'DatabaseMySQLex';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	public function __construct( $server, $user, $password, $dbName, $failFunction, $flags )
	{ 
		global $wgExtensionCredits;
		
		$wgExtensionCredits[self::thisType][] = array(
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
		global $wgDBclass, $wgDBtype;
		if (!isset( $wgDBclass)) return;
		
		$text = " \$wgDBtype is set to <b>{$wgDBtype}</b> and \$wgDBclass is set to a <b>{$wgDBclass}</b>."
		$this->updateCreditsDescription( $text );			
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

	// from MW 1.10 RC1
	public function query( $sql, $fname = '', $tempIgnore = false ) 
	{
		global $wgProfiling;

		if ( $wgProfiling ) {
			# generalizeSQL will probably cut down the query to reasonable
			# logging size most of the time. The substr is really just a sanity check.

			# Who's been wasting my precious column space? -- TS
			#$profName = 'query: ' . $fname . ' ' . substr( Database::generalizeSQL( $sql ), 0, 255 );

			if ( is_null( $this->getLBInfo( 'master' ) ) ) {
				$queryProf = 'query: ' . substr( Database::generalizeSQL( $sql ), 0, 255 );
				$totalProf = 'Database::query';
			} else {
				$queryProf = 'query-m: ' . substr( Database::generalizeSQL( $sql ), 0, 255 );
				$totalProf = 'Database::query-master';
			}
			wfProfileIn( $totalProf );
			wfProfileIn( $queryProf );
		}

		$this->mLastQuery = $sql;

		# Add a comment for easy SHOW PROCESSLIST interpretation
		#if ( $fname ) {
			global $wgUser;
			if ( is_object( $wgUser ) && !($wgUser instanceof StubObject) ) {
				$userName = $wgUser->getName();
				if ( strlen( $userName ) > 15 ) {
					$userName = substr( $userName, 0, 15 ) . '...';
				}
				$userName = str_replace( '/', '', $userName );
			} else {
				$userName = '';
			}
			$commentedSql = preg_replace('/\s/', " /* $fname $userName */ ", $sql, 1);
		#} else {
		#	$commentedSql = $sql;
		#}

		# If DBO_TRX is set, start a transaction
		if ( ( $this->mFlags & DBO_TRX ) && !$this->trxLevel() && 
			$sql != 'BEGIN' && $sql != 'COMMIT' && $sql != 'ROLLBACK' 
		) {
			$this->begin();
		}

		if ( $this->debug() ) {
			$sqlx = substr( $commentedSql, 0, 500 );
			$sqlx = strtr( $sqlx, "\t\n", '  ' );
			wfDebug( "SQL: $sqlx\n" );
		}

		# Do the query and handle errors
		$ret = $this->doQuery( $commentedSql );

		# Try reconnecting if the connection was lost
		if ( false === $ret && ( $this->lastErrno() == 2013 || $this->lastErrno() == 2006 ) ) {
			# Transaction is gone, like it or not
			$this->mTrxLevel = 0;
			wfDebug( "Connection lost, reconnecting...\n" );
			if ( $this->ping() ) {
				wfDebug( "Reconnected\n" );
				$sqlx = substr( $commentedSql, 0, 500 );
				$sqlx = strtr( $sqlx, "\t\n", '  ' );
				global $wgRequestTime;
				$elapsed = round( microtime(true) - $wgRequestTime, 3 );
				wfLogDBError( "Connection lost and reconnected after {$elapsed}s, query: $sqlx\n" );
				$ret = $this->doQuery( $commentedSql );
			} else {
				wfDebug( "Failed\n" );
			}
		}

		if ( false === $ret ) {
			$this->reportQueryError( $this->lastError(), $this->lastErrno(), $sql, $fname, $tempIgnore );
		}

		if ( $wgProfiling ) {
			wfProfileOut( $queryProf );
			wfProfileOut( $totalProf );
		}
		return $ret;
	} // end query.


} // end class definition.
?>