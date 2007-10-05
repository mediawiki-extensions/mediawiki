<?php
/**
 * @author Jean-Lou Dupont
 * @package HNP
 */
//<source lang=php>
#require 'HNP.i18n.php';

class HNP
{
	const thisName = 'HNP';
	const thisType = 'other';

	const rRead    = "read";
	const rEdit    = "edit";
	
	static $msg;
	const mPage    = "MediaWiki:Registry/HNP";	

	// STATUS related
	static $LoadedFromRegistryPage = false;
	static $permissionsLoadedFromFileCache = false;
	static $LoadedFromCache = false;

	// PERMISSIONS en-force currently (raw form)
	static $permissions = array();
	static $groupRights = array();
	static $groupHier   = array();	

	// PERMISSIONS en-force currently (processed form)
	static $perms  = array(); // without wildcards
	static $permsW = array(); // with wildcards
	static $rights = array();
	static $ghier  = array();
	
	static $rightsNsI = array(); // namespace independant
	static $rightsNsD = array(); // namespace dependant
	
	// PERMISSIONS being defined on the current page.
	static $new_permissions = array();
	static $new_groupRights = array();
	static $new_groupHier   = array();		

	// TABLE FORMATTING related
	static $columnSeparator = "||";
	static $rowStart = "|";
	static $rowEnd   = "|-";

	// TEMPLATE related
	static $thisDir;
	const tPage 	= "/HNP.template.en";

	// CACHE related
	static $expiryPeriod = 86400;	//24*60*60 == 1day
	static $realCache = true; 		// assume we get a real cache.
	static $cache;
	static $fileCacheName = null;
	const  fileCacheFileName = 'hnp_cache.serialized';

	/**
	 */
	public function __construct()
	{
		self::$thisDir = dirname( __FILE__ );
		self::$fileCacheName = self::$thisDir.'/'.self::fileCacheFileName;
		
		self::initCacheSupport();
		self::readPermissions();
		self::processPermissions();
	}
	/**
		{{#hnp:group|namespace|title|right}}
	 */
	public function mg_hnp( &$parser, $group, $ns, $title, $right, $notes = null)
	{
		self::$new_permissions[$group][] = array(	'ns' 	=> $ns,
													'title' => $title,
													'right' => $right
											);	
		// Format a nice wikitext line
		return	self::$rowStart.
				$group.self::$columnSeparator.
				$ns.self::$columnSeparator.
				$title.self::$columnSeparator.				
				$right.self::$columnSeparator.				
				$notes."\r\n".
				self::$rowEnd."\r\n";
	}
	/**
		{{#hnp_r: right | type }}
	 */
	public function mg_hnp_r( &$parser, $right, $type, $notes = null )
	{
		$type = strtoupper( $type );
		
		// basic checks
		if ( ($type !== 'D' ) && ($type !== 'I' ))
			$type = '??';
		else
			self::$new_groupRights[$right] = $type;
		
		// Format a nice wikitext line		
		return	self::$rowStart.
				$right.self::$columnSeparator.
				$type.self::$columnSeparator.
				$notes."\r\n".
				self::$rowEnd."\r\n";
	}
	/**
		{{#hnp_h: groupx, groupy, ... }}
	 */
	public function mg_hnp_h( &$parser, $groupList )
	{
		if (empty( $groupList ))
			return '';
		
		$liste = explode( ',', $groupList );
		
		self::$new_groupHier = $liste;
		
		// Format a nice wikitext line
		$fliste = implode( ',', $liste );
				
		return $fliste;
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
// MAIN HOOKS

	/**
		This is a hook that must be installed in 'User.php'.
	 */
	function hUserIsAllowed( &$user, $ns=null, $titre=null, $action, &$result )
	{
		 // disallow by default.
		$result = false;
		if ($action == '') return false;
		
		// some translation required.
		if ($action == 'view' ) $action = 'read';
		
		// Namespace independant right ??
		if ( in_array( $action, self::$rightsNsI ) )
		{
			$result = $this->userCanInternal( $user, '~', '~' , $action );
			return false;	
		}

		// debugging...
		if (! in_array( $action, self::$rightsNsD) )
		{
			echo "HNP: action <b>$action</b> not found in namespace dependant array. \n";
			return false;	
		}

		// Namespace dependant right:
		// Two cases:
		// 1) the request comes from a stock Mediawiki method that does not know about HNP
		//    * request might come from a SpecialPage context.
		//
		// 2) the request comes from an HNP aware method somewhere.
		
		// are we asked to check for a specific action in a specific namespace??

		global $wgTitle;
		if (!is_object( $wgTitle ))
			return false;
			
		$cns = $wgTitle->getNamespace();
		$cti = $wgTitle->mDbkeyform;

		// Does the request come from NS_SPECIAL and namespace dependant??		
		if ( ($cns == NS_SPECIAL) && ($ns === null) )
		{
			echo "HNP: action <b>$action</b> namespace dependent but called from NS_SPECIAL. <br/>\n";
#			var_dump( debug_backtrace() );
			return false;	
		}

		// Finally, the request comes from a valid namespace & with a valid namespace dependent action
		if ( $ns === null )    $ns = $cns;
		if ( $titre === null ) $titre = $cti;

		// Deal with page level restrictions
		if (!$this->checkRestrictions( $user, $wgTitle, $ns, $titre, $action ))
		{
			$result = false;
			return false;	
		}

#		echo 'user='.$user->getName().'ns= '.$ns.' titre='.$titre.' action='.$action.'<br/>';

		$result = $this->userCanInternal( $user, $ns, $titre , $action );
	
		// stop hook chain.
		return false;
	}
	/**
		This is the stock MediaWiki 'userCan' hook.
		
		t-> title, u-> user, a-> action, r-> result
	 */
	function huserCan( &$t, &$u, $a, &$r )
	{
		// disallow by default.
		$r = false;
		
		// some translation required.
		if ($a == 'view' ) $a = 'read';
		
		// Can the user perform a read operation?
		$ns = $t->getNamespace();
		$pt = $t->mDbkeyform;

#		echo " page: ".$pt." ns: ".$ns." action: ".$a."\n";

		// Deal with page level restrictions
		if (!$this->checkRestrictions( $u, $t, $ns, $pt, $a ) )
			return false;	

		// Normal processing path.
		$r = $this->userCanInternal( $u, $ns, $pt, $a );
		
		// don't let other extensions override this result.			
		return false; 
	}
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	/**
	 */
	public static function buildPermissionKey( $ns, $pt, $a )
	{
		return "/^$ns\|$pt\|$a".'$/siU';
	}	
	/**
	 */
	protected function userCanInternal( &$user, $ns, $pt, $a )
	{
		
		// Always allow login/logout!
		if ( (($pt == 'Userlogin') || ($pt=='Userlogout')) && 
				($ns==NS_SPECIAL) && ($a=='read') )
		{	
			$r = true;
			return false;
		}
		// Also always allow the sysop in !
		if ($this->isUserPartOfGroup( $user, 'sysop') && ($a != 'bot' ))
			return true;		
		
		// NOTE: the term "group" is somewhat confusing.
		//       Use the following semantic to interpret:
		//       " User X is part of Group Y if X can
		//        perform Action A on the Page T of
		//        Namespace NS "
		// A User with Rights in the sub-space X\Y\* (as example)
		// is entitled *only* (assuming no other superset group is
		// defined for this User) to this sub-space i.e.
		// User can not have access to higher level pages e.g. X\*
		//
		
		// disallow by default.
		$r = false;
		
		foreach ( self::$groupHier as $index => $group )
		{
			// is the user part of the group?
			if ( !self::isUserPartOfGroup( $user, $group ) ) 
				continue;
			
			$groupa = array( $group );
			$grights = $user->getGroupPermissions( $groupa ); 

			// FIRST GROUP OF TESTS
			//   EXCLUDE ACTION tests
			$eqs = self::buildPermissionKey( $ns, $pt, "!${a}" );		
			$r = self::testRightsWildcard( $eqs, self::$perms[$group] /* without wildcards */ );
			if ($r) return false;		
		
			// SECOND GROUP OF TESTS
			// ---------------------
			// Go through all the group membership and
			// extract the rights looking for the ones
			// dynamically created (e.g. by this extension i.e. createGroups)
			// which are compatible with this extension.
			$qs = self::buildPermissionKey( $ns, $pt, $a );
			$r = self::testRightsWildcard( $qs, self::$permsW[$group] );
			if ($r) return true;		
		}

		// If all tests fail, then conclude the user does not have the required right.
		return $r;
	}


// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	/**
	 */
	protected function processPermissions()
	{
		// Permissions
		if (!empty(self::$permissions))
			foreach (self::$permissions as $group => &$entries )
			{
					$with_wildcards = null;
					$without_wildcards = null;
					$this->formatEntries( $entries, $with_wildcards, $without_wildcards );
					self::$permsW[$group] = $with_wildcards;
					self::$perms[$group] = $without_wildcards;					
			}
			
		// Rights
		if (!empty(self::$groupRights))
			foreach (self::$groupRights as $right => &$type )
			{
				if ( $type === 'D' )
					self::$rightsNsD[] = $right;
				else
					self::$rightsNsI[] = $right;				
			}
		// Hierarchy
		// ** already formatted correctly.
	}
	protected function formatEntries( &$entriesForGroup, &$with, &$without )
	{
		foreach ( $entriesForGroup as $index => &$entry )
		{
			$foundWildcard = false;
			
			// 1- Namespace
			// First, check if we have a 'wildcard'
			$ns_name = trim( $entry['ns'] );			
			if ( $ns_name === '~' )
				$nsField = "(.*)";
			else
			{
				$nsIndex = $this->getNsIndex( $entry['ns'] );
				$nsField = $nsIndex;
			}
			// 2- Title
			// First, check if we have a 'wildcard'
			$title_name = preg_quote( trim( $entry['title'] ) );
			$titleField = str_replace('/', '\/', $title_name );
			$titleField = str_replace("~", "(.*)", $titleField );
			
			// 3- Action
			// Can be a list e.g. read, edit, browse
			$right_name = preg_quote( trim( $entry['right'] ));
			
			// We are not supposed to find '/' but just make sure
			// we don't break.
			$rightField = str_replace('/','\/',$right_name);
			
			$foundWilcard = strpos( $right_name, '~' );
			
			$rightField = str_replace("~", "(.*)", $rightField);
			
			$rights = explode(",", $rightField);
			$linePiece = '/^'.$nsField.'\|'.$titleField.'\|';
			foreach($rights as $r)
			{
				$line = $linePiece.$r.'$/siU';
				if ($foundWilcard === false)
					$without[] = $line;
				$with[] = $line;
			}
		}
		
	}
	protected function getNsIndex( $name )
	{
		$name = strtolower( $name );
		if ( ($name ==='') || ($name === 'main'))
			return 0;
		return Namespace::getCanonicalIndex( $name );
	}
	/**
	 */
	protected function readPermissions()
	{
		// try the cache first!
		$result = $this->readPermissionsFromCache();
		self::$LoadedFromCache = $result;
		if ($result === true)	
			return true;
		

		// else, let's parse the file cache...
		$result = $this->readFromFileCache();
		self::$permissionsLoadedFromFileCache = $result;
		
		return false;
	}
	protected function readFromFileCache()
	{
		$contents = @file_get_contents( self::$fileCacheName );
		$us = @unserialize( $contents );
		if ($us === false)
			return false;
		$this->formatFromUnserialized( $us );
		return true;
		
	}
	/**
	 */
	protected static function writeToFileCache( &$data )
	{
		$s = serialize( $data );
		$bytes_written = @file_put_contents( self::$fileCacheName, $s );
	}
	/**
		Words in pair with 'readPermissionFromCache'
	 */
	protected function updatePermissions()
	{
		$p = array( 'groups' => self::$new_permissions,
					'rights' => self::$new_groupRights,
					'hier'   => self::$new_groupHier
				 );	
				 
		self::writeToCache( $p );
		self::writeToFileCache( $p );	
	}
	/**
		Works in pair with 'updatePermissions'
	 */
	protected function readPermissionsFromCache()
	{
		$us = self::readFromCache();
		if ($us === false)
			return false;
			
		$this->formatFromUnserialized( $us );
		
		return true;
	}
	protected function formatFromUnserialized( &$us )
	{
		self::$permissions = $us['groups'];
		self::$groupRights = $us['rights'];
		self::$groupHier   = $us['hier'];		
	}
	/**
	 */
	static function initCacheSupport()
	{
		self::$cache = & wfGetMainCache();	

		if (self::$cache instanceof FakeMemCachedClient)
			self::$realCache = false;
	}

	/**
	 */
	static function writeToCache( &$data )
	{
		if (!self::$realCache)
			return false;
			
		$key = self::getKey();
			
		$s = serialize( $data );
		
		self::$cache->set( $key, $s, self::$expiryPeriod );
	}
	/**
	 */
	static function readFromCache( )
	{
		if (!self::$realCache)
			return false;

		$key = self::getKey();
				
		$s = self::$cache->get( $key );

		return @unserialize( $s );
	}
	/**
		Formats a unique key for the cache.
	 */
	static function getKey( )
	{
		return '~#HNP#~';
	}
	/**
	 */
	static function realCacheStatus( &$state )
	{
		$state = self::$realCache;
		return (self::$realCache ? 'true':'false');
	}
	static function isFileCacheWritable()
	{
		return is_writable( self::$fileCacheName );	
	}
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
// HOOKS
	/**
	 */
	public function hArticleSave(	&$article, &$user, &$text, &$summary, 
									$minor, $dontcare1, $dontcare2, &$flags )
	{
		// Paranoia: this should have already been checked.
		// does the user have the right to edit pages in this namespace?
		if (! $article->mTitle->userCan(self::rEdit) ) return true;  

		// Are we dealing with the page which contains the links to manage?
		if ( $article->mTitle->getFullText() != self::mPage ) return true;

		// Invoke the parser in order to retrieve the interwiki link data
		// composed through the magic word 'iwl'
		$this->parse( $article->mTitle, $text );
											
		$result = $this->updatePermissions();

		#$summary = count(self::$new_permissions);
		
		return true; // continue hook-chain.
	}
	/**
		This hook is called to preload text upon initial page creation.
	 */
	public function hEditFormPreloadText( &$text, &$title )
	{
		// Are we dealing with the page which contains the links to manage?
		if ( $title->getFullText() != self::mPage ) return true;
		
		// Paranoia: Is the user allowed committing??
		// We shouldn't even get here if the 'edit' permission gets
		// verified adequately.
		if (! $title->userCan(self::rEdit) ) return true;		

		// start the user with a nice template.
		$text = $this->getTemplate();
	
		// stop hook chain.
		return false;
	}
	/**
	 */
	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		$result1 = ' Using caching: ';
		$result1 .= self::$realCache ? 'true.':"<b>false</b>.";
		
		$result2 = ' Permissions loaded from cache: ';
		$result2 .= self::$LoadedFromCache ? 'true.':"<b>false</b>.";

		$result3 = ' File cache writable: ';
		$result3 .= self::isFileCacheWritable() ? 'true.':"<b>false</b>.";

#		$result3 = ' Permissions loaded from registry: ';
#		$result3 .= self::$LoadedFromRegistryPage ? 'true.':"<b>false</b>.";
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1.$result2.$result3;
				
		return true; // continue hook-chain.
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	
	/**
		For now, only one template is supported.
	*/
	protected function getTemplate()
	{
		$filePath = self::$thisDir . self::tPage;
		$contents = @file_get_contents( $filePath );
		return $contents;
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
		Parses a page.
		
		This method does the dirty work of extracting
		the configuration from the wikitext.	
	 */
	protected function parse( &$title, &$text )	
	{
		global $wgParser, $wgUser;
		$popts = new ParserOptions( $wgUser );
		$parserOutput = $wgParser->parse(	$text, 
											$title, 
											$popts, 
											true, true, 
											null );
	}
/*	
	protected function processRegistryPage( )
	{
		$text = $this->getRegistryPageContents( $title );
		if (empty( $text ))
			return false;
		
		$sd = $this->extractSerializedData( $text );
	
		$a = @unserialize( $sd );
	
		//FIXME ...
	
		$result = false;
		
		// after parsing the page, the permissions
		// should be in the class variables
		if (!empty( self::$new_permissions) || 
			!empty( self::$new_groupRights) )
		{
			$result = true;
			self::$permissions = self::$new_permissions;
			self::$groupRights = self::$groupRights;
		}
		
		return $result;
	}
	protected function extractSerializedData( &$text )
	{
		
	}
	
	protected function getRegistryPageContents( &$title )
	{
		$contents = null;
		$title = Title::newFromText( self::mPage );
		$rev = Revision::newFromTitle( $title );
		if( $rev )
		    $contents = $rev->getText();		
			
		return $contents;
	}	
*/	


// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 */
	static function testRightsWildcard( $q, $rights )
	{	
		if (empty($rights))
			return false;
		
		// Go through each right
		// and look if the query matches with it
		// In reality, the array $rights is (should be!) already
		// formatted for use with the matching function, acting as
		// the pattern in question.
		foreach ($rights as $pattern)
		{
			$result = preg_match( $pattern, $q );
#			echo __METHOD__." pattern: ".$pattern." ... q: ".$q." result: $result \n";			
			if ($result === 1)
				return true;	
		}
		return false;		
	}

	/**
	 */
	public static function isUserPartOfGroup( &$user, $group )
	{
		if (empty( $group )) return false;
		
		return in_array( $group, $user->getEffectiveGroups() );
	}
	/**
	 */
	private function checkRestrictions( &$user, &$title, &$ns, &$titre, &$action )
	{
		if ( !is_object( $title ) )
			return true;

		// Load Page level restrictions
		$restrictions = $title->getRestrictions($action);
		if (empty($restrictions))
			return true;
			
		foreach( $restrictions as $group )
			if (self::isUserPartOfGroup( $user, $group ))
				return true;
		
		// didn't find any restrictions that weren't met with the proper right.
		return true;
	}


} // end class definition.
//</source>