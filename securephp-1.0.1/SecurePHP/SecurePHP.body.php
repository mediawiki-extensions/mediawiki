<?php
/**
	@author Jean-Lou Dupont
	@package SecurePHP	
 */
//<source lang=php>
class SecurePHP
{
	const thisType = 'other';
	const thisName = 'SecurePHP';
	
	public function __construct() 
	{}
	
	public function tag_runphp( &$code, &$params, &$parser )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'SecurePHP: '.wfMsg('badaccess');
			
		return self::executeCode( $code );
	}
	
	/**
		1- IF the page is protected for 'edit' THEN allow execution
		2- IF the page's last contributor had the 'coding' right THEN allow execution
		3- ELSE deny execution
	 */
	private static function checkExecuteRight( &$title )
	{
		if ($title->isProtected('edit'))
			return true;
		
		global $wgUser;
		if ($wgUser->isAllowed('coding'))
			return true;
		
		// Last resort; check the last contributor.
		$rev    = Revision::newFromTitle( $title );
		
		$user = User::newFromId( $rev->mUser );
		$user->load();
		
		if ($user->isAllowed( 'coding' ))
			return true;
		
		return false;
	}
	
	/**
		Actually execute the code provided.
		
		Optionally, executes a callback function is some
		arguments are passed to the function.
	 */
	private static function executeCode( &$code, &$argv = null)
	{
		# start capturing the user code's output
		ob_start();
		
		# can't pass arguments directly with 'eval'
		# must load the code in the PHP interpreter and
		# get a callback function name returned.
		// NOTE: 'eval' does not mind being passed 
		// a 'null' parameter
		$callback = eval( $code );
		
		# look for arguments.
		if ( count($argv)>0 )
			call_user_func( $callback, $argv );
		
		$output = ob_get_contents();
		
		ob_end_clean();
		
		return $output;
	}
	
} // end class
//</source>
