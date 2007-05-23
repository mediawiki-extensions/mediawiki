<?php
/*
 * SmartyAdaptorClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 */

class SmartyAdaptorClass extends ExtensionClass
{
	// constants.
	const thisName = 'Smarty Adaptor';
	const thisType = 'other';
	const marker   = 'smarty';
	const typeProc = 1;
	const typeTpl  = 2;

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
	} 
	public function mg_smarty( &$parser, $proc, $tpl )  
	{
		// check processor script availability
		$r1 = $this->checkFile( $proc, self::typeProc );
		if ($r1 === false) 
			$m1 = wfMsgForContent( 'smartyadaptor-proc-filenotfound', $proc );
			
		// check template script availability
		$r2 = $this->checkFile( $tpl, self::typeTpl );
		if ($r2 === false) 
			$m2 = wfMsgForContent( 'smartyadaptor-tpl-filenotfound', $tpl );			

		if ( ($r1 === false) || ($r2 === false))
			return $m1.'<br/>'.$m2;
		
		// prepare marker
		$marker = "_".self::$marker."_($proc)($tpl)_/".self::$marker.'_';
		
		// insert 'marker' for function the hook 'OutputPageBeforeHTML'
		return $marker;		
	}
	private function checkFile( $file, $type )
	{
		switch( $type )
		{
			case self::typeProc:
				$fichier = $IP.self::$base.'/'.self::$procs.'/'.$file;
			break;			
			case self::typeTpl:
				$fichier = $IP.self::$base.'/'.self::$tpls.'/'.$file;			
			break;
		}
		return file_exists( $fichier );
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
		if ( ($r===0) || ( $r===false)) return true; 
	
		// go through all matches & replace associated marker with result
		// full match: $m[0]
		// proc: first sub-patterns array -> $m[1]
		// tpl: second sub-patterns array -> $m[2]
		foreach ($m[0] as $index => &$fullMatch)
		{
			$proc = $m[1][$index];
			$tpl  = $m[2][$index];
			
			// execute the template processor
			
			// replace full match with output of processor
			
		}
	
		return true; // continue hook chain.
	}	
} // END CLASS DEFINITION
?>