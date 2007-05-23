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
	const typeCfg  = 3;

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

	// smarty config files directory
	// (relative to $base)
	static $cfgs  = '/configs';
	
	// {{#smarty: ... }}
	static $mgwords = array('smarty');
	
	// marker pattern
	var $markerPattern;	
	
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
		
		$this->markerPattern = "/_".self::$marker.'_\((.*)\)\((.*)\)\((.*)\)_\/'.self::$marker."_/si";
		
		// Messages.
		global $wgMessageCache, $wgSmartyAdaptorMessages;
		foreach( $wgSmartyAdaptorMessages as $key => $value )
			$wgMessageCache->addMessages( $wgSmartyAdaptorMessages[$key], $key );
	} 
	public function mg_smarty( &$parser, $proc, $tpl, $config )  
	{
		// check processor script availability
		$r1 = $this->checkFile( $proc, self::typeProc );
		if ($r1 === false) 
			$m1 = wfMsgForContent( 'smartyadaptor-proc-filenotfound', $proc );
			
		// check template script availability
		$r2 = $this->checkFile( $tpl, self::typeTpl );
		if ($r2 === false) 
			$m2 = wfMsgForContent( 'smartyadaptor-tpl-filenotfound', $tpl );			

		// check template script availability
		$r3 = $this->checkFile( $config, self::typeCfg );
		if ($r3 === false) 
			$m3 = wfMsgForContent( 'smartyadaptor-cfg-filenotfound', $config );			

		if ( ($r1 === false) || ($r2 === false) || ($r3 === false) )
			return $m1.'<br/>'.$m2.'<br/>'.$m3;
		
		// prepare marker
		$marker = "_".self::$marker."_($proc)($tpl)($cfg)_/".self::$marker.'_';
		
		// insert 'marker' for function the hook 'OutputPageBeforeHTML'
		return $marker;		
	}
	private function checkFile( $file, $type )
	{
		return file_exists( $this->getFilename( $file, $type) );
	}
	private function getFilename( $name, $type )
	{
		switch( $type )
		{
			case self::typeProc:
				$fichier = $IP.self::$base.self::$procs.'/'.$file.'.php';
			break;			
			case self::typeTpl:
				$fichier = $IP.self::$base.self::$tpls.'/'.$file.'.tpl';			
			break;
		}
		return $fichier;
	}
	function hOutputPageBeforeHTML( &$op, &$text )
	/*  This hook will call the processing script(s).
	 */
	{
		// marker form:
		// _smarty_(proc)(tpl)_/smarty_
		$r = preg_match_all( $this->markerPattern, $text, $m );

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
			
			// load template processor
			require( $this->getFilename($proc, self::typeProc) );
			
			// make sure the class exists before going further
			if ( !class_exists( $proc ) )
			{
				$errMsg = wfMsgForContent( 'smartyadaptor-class-notfound', $proc );
				$this->replaceMarker( $fullMatch, $errMsg, $text ); 
				continue; // give a chance to find other problems
			}
			
			// instantiate a template processor object
			$pObj = new $proc( );

			// provision the processor
			$pObj->template_dir = GUESTBOOK_DIR . 'templates';
			$pObj->compile_dir  = GUESTBOOK_DIR . 'templates_c';
			$pObj->config_dir   = GUESTBOOK_DIR . 'configs';
			$pObj->cache_dir    = GUESTBOOK_DIR . 'cache';

			// get the result
			$output = $pObj->fetch( $tpl );
			
			// replace full match with output of processor
			$this->replaceMarker( $fullMatch, $output, $text );
			
		} // end foreach
	
		return true; // continue hook chain.
	}
	private function replaceMarker( $m, $r, &$subject )
	{	$subject = preg_replace( $m, $r, $subject );	}
	
} // END CLASS DEFINITION
?>