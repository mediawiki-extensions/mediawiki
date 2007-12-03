<?php
/**
 * @author Jean-Lou Dupont
 * @package PageTrail
 * @version 1.0.5
 * @Id $Id: PageTrail.body.php 714 2007-12-03 19:53:48Z jeanlou.dupont $
 */
//<source lang=php>
require 'PageTrail.i18n.php';

class PageTrail
{
	static $msg = array();
	static $max_count = 5;
	static $delimiter = ' &gt; ';
	
	var $trail = array();
	var $enable = true;

	public function __construct()
	{
		global $wgUser;
 
	    $this->enable = $wgUser->getOption('page_trail') != 0;

		$this->registerMessages();
	}
	protected function registerMessages()
	{
	    global $wgMessageCache;		
		if (!empty( self::$msg ))
			foreach( self::$msg as $key => $value )
				$wgMessageCache->addMessages( self::$msg[$key], $key );		
		
	}
	public function hUserToggles( &$arr )
	{
	    $arr[] = 'page_trail';
	    return true;
	}
	public function hSiteNoticeAfter( &$sitenotice )
	{
		if ( $this->enable )
			$sitenotice .= $this->buildTrail();
		return true;
	}
	public function hBeforePageDisplay( &$op )
	{
		if ( $this->enable )
		{
			$style = "\n".
'<style type="text/css">
	#PageTrail {
    font-size:0.8em;
    background-color: #FFFFCC;
    position:absolute;
    left: 2px;
    bottom:0;
    width:99%;
</style>';
		
			$op->addScript( $style );
		}		
		return true;
	}
	protected function buildTrail()
	{
		global $wgTitle, $wgUser;
		
		$title = $wgTitle->getPrefixedText();
		$trail = array();
		
		$serialized_data = $this->getCookie();
		
		if ( $serialized_data !== null )
			$trail = unserialize( $serialized_data );

	    #if returning to a page we've already visited, reduce the array
		if (!empty( $trail ))
		{
		    $loc = array_search( $title, $trail );
		    if ($loc !== false)
				$trail = array_slice($trail, 0, $loc );
 		}
	    # if we've got too many entries, reduce the array:
	    if( count( $trail ) > self::$max_count) 
			array_shift( $trail );
		
		array_push( $trail, $title );

	    # serialize data from array to session:
	    $this->setCookie( serialize( $trail ) );
		
	    $count = count( $trail );
 
	    $m_skin =& $wgUser->getSkin();

	    $line = "<div id='PageTrail'>&nbsp;<i>Page Trail:</i> ";

		if ( !empty( $trail ))
			foreach( $trail as $index => &$e )
			{
				$line .= $m_skin->makeLink( $e );
		  		if ( $index < ($count-1) )
				  	$line .= self::$delimiter;
	    	}
			
	    $line .= '&nbsp;</div>';
	
		return $line;
	}	
	function setCookie( &$value ) 
	{
		global $wgCookieExpiration, $wgCookiePath, $wgCookieDomain, $wgCookieSecure, $wgCookiePrefix;

		$exp = time() + $wgCookieExpiration;

		$_SESSION['pagetrail'] = $value;
		setcookie( $wgCookiePrefix.'pagetrail', $value, $exp, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );
	}
	function getCookie()
	{
		if (isset( $_SESSION['pagetrail']))
			return $_SESSION['pagetrail'];
			
		return null;		
	}
	
}//end class
//</source>