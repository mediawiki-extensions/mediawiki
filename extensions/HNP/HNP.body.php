<?php
/**
 * @author Jean-Lou Dupont
 * @package HNP
 */
//<source lang=php>
require 'HNP.i18n.php';

class HNP
{
	const thisName = 'InterWikiLinkManager';
	const thisType = 'other';

	const rRead    = "read";
	const rEdit    = "edit";
	
	static $msg;
	const mPage    = "MediaWiki:Registry/HNP";	

	// PERMISSIONS en-force currently
	static $permissions = null;
	static $groupRights = null;
	
	// PERMISSIONS being defined on the current page.
	static $new_permissions = null;
	static $new_groupRights = null;

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
	public static function __construct()
	{
		self::$thisDir = dirname( __FILE__ );
		self::initCacheSupport();
		self::loadPermissions();
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
		return self::$rowStart.$group.$columnSeparator.
				$ns.$columnSeparator.
				$right."\n".self::$rowEnd."\n";
	}
	/**
		{{#hnp_r: right | type }}
	 */
	public function mg_hnp_r( &$parser, $right, $type )
	{
		self::$new_groupRights[$right] = $type;
		
		return self::$rowStart.$right.$columnSeparator.
				$type."\n".self::$rowEnd."\n";
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
	protected function updatePermissions()
	{
		$p = array( 'groups' => self::$new_permissions,
					'rights' => self::$new_groupRights
				 );	
		$s = serialize( $p );
	
		self::writeToCache( $s );		
	}
	protected function readPermissions()
	{
		$s = self::readFromCache();
		if ($s === false)
			return false;
			
		$us = unserialize( $s );
		
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
			
		$s = serialize( $exts );
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
		global $wgParser, $wgUser;
		$popts = new ParserOptions( $wgUser );
		$parserOutput = $wgParser->parse(	$text, 
											$article->mTitle, 
											$popts, 
											true, true, 
											$article->mRevision );
											
		$result = $this->updatePermissions();

		// 
		#$summary = $result;
		
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
		For now, only one template is supported.
	*/
	protected function getTemplate()
	{
		$filePath = self::$thisDir . self::tPage;
		$contents = @file_get_contents( $filePath );
		return $contents;
	}
	
} // end class definition.
//</source>