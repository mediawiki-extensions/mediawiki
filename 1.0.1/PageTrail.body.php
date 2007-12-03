<?php
/**
 * @author Jean-Lou Dupont
 * @package PageTrail
 * @version 1.0.1
 * @Id $Id: PageTrail.body.php 710 2007-12-03 17:23:15Z jeanlou.dupont $
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
			$style = '<style type="text/css"><![CDATA[
					#PageTrail {
					        font-size:0.8em;
					        background-color: #FFFFCC;
					        position:absolute;
					        left: 2px;
					        bottom:0;
					        width:99%;
					}]]></style>';
					
			$op->addScript( $style );
		}		
		return true;
	}
	protected function buildTrail()
	{
		global $wgTitle, $wgUser;
		
		$title = $wgTitle->getPrefixedText();
		$trail = '';
		
	    //If a session doesn't already exist, create one
	    if( isset( $_SESSION['pagetrail'] ) )
	      $trail = $_SESSION['pagetrail'];
	    else 
		{
	      if( !isset( $_SESSION ) )
	        session_start();
	      $_SESSION['pagetrail'] = array();
	    }
	    # cache index of last element:
	    $count = count( $trail ) - 1;
 
	    # if we've got too many entries, reduce the array:
	    if( count( $trail ) > 0 && $trail[ $count ] != $title ) 
	      $trail = array_slice( $trail, ( 1 - self::$max_count ) );
		
		array_push( $trail, $title );
 
	    #if returning to a page we've already visited, reduce the array
	    $loc = array_search( $title, $trail );
	    if(($loc >= 0))
	      $trail = array_slice($trail, 0, ($loc + 1));
 
	    # serialize data from array to session:
	    $_SESSION['pagetrail'] = $trail;
	    # update cache:
	    $count = count( $trail ) - 1;
 
	    $m_skin =& $wgUser->getSkin();

	    $line = "<div id='PageTrail'>&nbsp;<i>Page Trail:</i> ";
		
	    for( $i = 0; $i <= $count; $i++ ) 
		{
	      $line .= $m_skin->makeLink( $trail[$i] );
		  
	      if( $i < $count ) 
		  	$line .= self::$delimiter;
	    }
	    $line .= '&nbsp;</div>';
	
		return $line;
	}	
	
}//end class
//</source>