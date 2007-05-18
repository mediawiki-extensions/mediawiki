<?php
/*
 * ScriptsManagerClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * 
 */


class ScriptsManagerClass extends ExtensionClass
{
	// constants.
	const thisName = 'ScriptsManager';
	const thisType = 'other';  

	static $base = 'scripts/';

	public static function &singleton()
	{ return parent::singleton( );	}
	
	function ScriptsManagerClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( self::$mgwords, $passingStyle, $depth );

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.00 $id$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => ''
		);
	}
	public function setup() 
	{ 
		parent::setup();
	} 


} // END CLASS DEFINITION
?>