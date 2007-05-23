<?php
/*
 * SmartyAdaptorClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 *
 */

class SmartyAdaptorClass extends ExtensionClass
{
	// constants.
	const thisName = 'Smarty Adaptor';
	const thisType = 'other';
	const marker   = 'smarty';

	// base directory for this extension
	static $base  = 'scripts/smarty';

	// Smarty Framework files
	// (relative to $base)
	static $smarty = '/smarty';
	
	// smarty processor scripts directory
	// (relative to $base)
	static $procs = '/processors';
	
	// smarty templates directory
	// (relative to $base)
	static $tpls  = '/templates';
	
	// {{#smarty: ... }}
	static $mgwords = array('smarty');
	
	/*  Variables
	 */
	var $count;
	var $tlist;
	
	// error code constants

	public static function &singleton()
	{ return parent::singleton( );	}
	
	function SmartyAdaptorClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( self::$mgwords );

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.00 $Id$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Extension base directory: '.self::$base,
		);
	}
	public function setup() 
	{ 
		parent::setup();
		
		// Messages.
		global $wgMessageCache, $wgSmartyAdaptorMessages;
		foreach( $wgSmartyAdaptorMessages as $key => $value )
			$wgMessageCache->addMessages( $wgSmartyAdaptorMessages[$key], $key );
			
		// Init variables.
		$this->count = 0;
		$this->tlist = array();
	} 
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits;
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if ($el['name']==self::thisName)
				$el['description'].=$m;	
		
		return true; // continue hook-chain.
	}
	private function getMessage( $code )
	{
	}
	public function mg_smarty( &$parser, $proc, $tpl )  
	{
		// check processor script availability
		
		// check template script availability

		// prepare marker
		$marker = "_".self::$marker."_($proc)($tpl)_/".self::$marker.'_';
		
		// insert 'marker' for function the hook 'OutputPageBeforeHTML'
		return $marker;		
	}
	function hOutputPageBeforeHTML( &$op, &$text )
	/*  This hook will call the processing script(s).
	 */
	{
		// marker form:
		// _smarty_(proc)(tpl)_/smarty_
		$p = "/_".self::$marker.'_\((.*)\)\((.*)\)_\/'.self::$marker."_/si";
		$r = preg_match_all( $p, $text, $m );

		// something to do?
		if ( ($r==0) || ( $r===false)) return true; 
	
		//
	
		return true; // continue hook chain.
	}	
} // END CLASS DEFINITION
?>