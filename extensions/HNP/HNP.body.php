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
	static $permissionsLoadedFromRegistryPage = false;
	static $permissionsLoadedFromCache = false;

	// PERMISSIONS en-force currently
	static $permissions = array();
	static $groupRights = array();
	
	// PERMISSIONS being defined on the current page.
	static $new_permissions = array();
	static $new_groupRights = array();

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

	/**
	 */
	public function __construct()
	{
		self::$thisDir = dirname( __FILE__ );
		self::initCacheSupport();
		self::readPermissions();
	}
	/**
		{{#hnp:group|namespace|title|right}}
	 */
	public function mg_hnp( &$parser, $group, $ns, $title, $right )
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
				$right."\r\n".
				self::$rowEnd."\r\n";
	}
	/**
		{{#hnp_r: right | type }}
	 */
	public function mg_hnp_r( &$parser, $right, $type )
	{
		self::$new_groupRights[$right] = $type;
		
		// Format a nice wikitext line		
		return	self::$rowStart.
				$right.self::$columnSeparator.
				$type."\r\n".
				self::$rowEnd."\r\n";
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	

	/**
		This is a hook that must be installed in 'User.php'.
	 */
	function hUserIsAllowed( &$user, $ns=null, $titre=null, $action, &$result )
	{
		
	}
	/**
		This is the stock MediaWiki 'userCan' hook.
		
		t-> title, u-> user, a-> action, r-> result
	 */
	function userCan( &$t, &$u, $a, &$r )
	{
		
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	/**
	 */
	protected function readPermissions()
	{
		// try the cache first!
		$result = $this->readPermissionsFromCache();
		self::$permissionsLoadedFromCache = $result;
		if ($result === true)	
			return true;
		

		// else, let's parse the registry page...
		/*
		$result = $this->processRegistryPage();
		self::$permissionsLoadedFromRegistryPage = $result;
		*/
		
		return false;
	}

	/**
	 */
	protected function updatePermissions()
	{
		$p = array( 'groups' => self::$new_permissions,
					'rights' => self::$new_groupRights
				 );	
				 
		self::writeToCache( $p );		
	}
	protected function readPermissionsFromCache()
	{
		$us = self::readFromCache();
		if ($us === false)
			return false;
			
		self::$permissions = $us['groups'];
		self::$groupRights = $us['rights'];
		
		return true;
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
		$us = @unserialize( $s );
		
		return $us;
	}
	/**
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

		// 
		$summary = count(self::$new_permissions);
		
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

		// start by reading the table from the database
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
		$result2 .= self::$permissionsLoadedFromCache ? 'true.':"<b>false</b>.";

#		$result3 = ' Permissions loaded from registry: ';
#		$result3 .= self::$permissionsLoadedFromRegistryPage ? 'true.':"<b>false</b>.";
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1.$result2;//.$result3;
				
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
} // end class definition.
//</source>