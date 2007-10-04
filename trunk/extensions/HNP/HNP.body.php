<?php
/**
 * @author Jean-Lou Dupont
 * @package HNP
 */
//<source lang=php>
require 'HNP.i18n.php';

class HNP
{
	static $msg;
	const mPage    = "MediaWiki:Registry/HNP";	

	/**
	 */
	public static function __construct()
	{
		self::initCacheSupport();
		
	}
	/**
		{{#hnp:group|namespace|title|right}}
	 */
	public function mg_hnp( &$parser, $group, $ns, $title, $right )
	{
		
	}
	/**
		{{#hnp_r: right | type }}
	 */
	public function mg_hnp_r( &$parser, $right, $type )
	{
		
	}
	/**
		Verify Namespace
	 */
	protected function validateEntry( $group, $right )
	{
		
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

	static $expiryPeriod = 86400;	//24*60*60 == 1day
	static $realCache = true; 		// assume we get a real cache.
	static $cache;

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
	static function writeToCache( $key, &$data )
	{
		if (!self::$realCache)
			return false;
			
		$s = serialize( $exts );
		self::$cache->set( $key, $s, self::$expiryPeriod );
	}
	/**
	 */
	static function readFromCache( $key )
	{
		if (!self::$realCache)
			return false;
		
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
	
} // end class definition.
//</source>