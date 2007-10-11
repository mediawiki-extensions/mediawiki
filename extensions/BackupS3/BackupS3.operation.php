<?php
/**
 * @author Jean-Lou Dupont
 * @package BackupS3
 * @version $Id$
 */
//<source lang=php>
/**
 * The actual hard-work happens here.
 */
class BackupS3Operation
{
	var $params;
	
	static $actionHandlers = array(
	
	);
	
	static function translateParams( &$op )
	{
		
	}
	
	public function __construct( &$params )
	{
		$this->params = $params;
			
	}
	/**
	 */	
	public function makeTitle( &$params )
	{
		
		return $title;
	}
	/**
	 */	
	public function run( &$errorMsg )
	{
		// assume worst case.
		$code = false;
	
	
		return $code;	
	}
	
} // end class BackupS3Operation

//</source>