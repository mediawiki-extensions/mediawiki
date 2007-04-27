<?php
/*
 * RequestTools.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a 'magic word' interface to retrieve
 *           useful 'WEB Request' level information.           
 *
 * Features:
 * *********
 *
 * {{#request: type=[int, text, bool] | key='GET parameter on URL' }} 
 *
 * E.g.:  {{#request:type=text | key=title}}
 *
 * DEPENDANCIES:
 * 1) 'ExtensionClass' extension
 *
 * Tested Compatibility:  MW 1.8.2, 1.9.3
 *
 * HISTORY:
 * -- Version 1.0:	initial availability
 *          
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'RequestTools Extension', 
	'version' => '1.0',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

// Let's create a single instance of this class
RequestTools::singleton();

class RequestTools extends ExtensionClass
{
	static $mgwords = array('request' );
	
	public static function &singleton( )
	{ return parent::singleton(); }
	
	// Our class defines magic words: tell it to our helper class.
	public function RequestTools()
	{	return parent::__construct( self::$mgwords );	}

	// ===============================================================

	public function mg_request( &$parser )
	{
		$params = $this->processArgList( func_get_args(), true );

		if (empty($params['key'])) return;

		$key  = $params['key'];
		$type = $params['type'];		
		
		global $wgRequest;
		
		switch ($type)
		{
			case 'int':
				return $wgRequest->getInt( $key );
			case 'bool':
				return $wgRequest->getBool( $key );
			case 'text':
			default:
				return $wgRequest->getText( $key );
		}
	}
} // end class	
?>