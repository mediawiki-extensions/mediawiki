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
	const typeProc  = 1;
	const typeTpl   = 2;
	const typeCfg   = 3;
	const typeComp  = 4;
	const typeCache = 5;

	static $ext = array(
		self::typeProc => 'php',
		self::typeTpl  => 'tpl',
		self::typeCfg  => 'cfg'
	);
	static $dirs = array(
		self::typeProc  => '/processors',
		self::typeTpl   => '/templates',
		self::typeComp  => '/templates/compile',
		self::typeCache => '/templates/cache',
		self::typeCfg   => '/configs',		
	);

	// The actual filename where Smarty's implementation lies.
	static $smartyClassFileName = '/libs/Smarty.class.php';
	static $smartyClassName     = 'Smarty';

	// base directory for this extension
	static $base  = 'scripts/smarty';

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
		
		$this->markerPattern = "/_".self::marker.'_\((.*)\)\((.*)\)\((.*)\)_\/'.self::marker."_/si";
		
		// Messages.
		global $wgMessageCache, $wgSmartyAdaptorMessages;
		foreach( $wgSmartyAdaptorMessages as $key => $value )
			$wgMessageCache->addMessages( $wgSmartyAdaptorMessages[$key], $key );
	} 
	
	/* ---------------------------------
	   Parser MAGIC WORD handling method
	   ---------------------------------
	*/
	public function mg_smarty( &$parser, $proc, $tpl, $config = null )  
	{
		// check processor script availability
		$r1 = $this->checkFile( $proc, self::typeProc );
		if ($r1 === false) $m1 = wfMsgForContent( 'smartyadaptor-proc-filenotfound', $proc );
			
		// check template script availability
		$r2 = $this->checkFile( $tpl, self::typeTpl );
		if ($r2 === false) $m2 = wfMsgForContent( 'smartyadaptor-tpl-filenotfound', $tpl );			

		// check template script availability
		if ( $config !==null )	{
		$r3 = $this->checkFile( $config, self::typeCfg );
		if ($r3 === false) $m3 = wfMsgForContent( 'smartyadaptor-cfg-filenotfound', $config ); }
		
		if ( ($r1 === false) || ($r2 === false) || ($r3 === false) )
			return $m1.'<br/>'.$m2.'<br/>'.$m3;
		
		// prepare the marker
		$marker = "_".self::marker."_($proc)($tpl)($cfg)_/".self::marker.'_';
		
		// insert 'marker' in the parsed text 
		// for the hook 'OutputPageBeforeHTML' to find.
		return $marker;		
	}
	/* ---------------------------------
	   Hook handler method
	   ---------------------------------
	*/
	function hOutputPageBeforeHTML( &$op, &$text )
	/*  This hook will call the processing script(s).
	 */
	{
		// marker form:
		// _smarty_(proc)(tpl)_/smarty_
		$r = preg_match_all( $this->markerPattern, $text, $m );

		// something to do?
		if ( ($r===0) || ( $r===false)) return true; 

		// let's load Smarty
		require( $IP.self::$base.'/'.self::$smartyClassFileName );

		// make sure we have the required class loaded.
		if ( !class_exists( self::$smartyClassName ) )
		{
			$errMsg = wfMsgForContent( 'smartyadaptor-smarty-classnotfound' );
 			$text .= '<br/'.$errMsg;
			return true;
		}
		
		// go through all matches & replace associated marker with result
		// full match: $m[0]
		// proc: first sub-patterns array -> $m[1]
		// tpl: second sub-patterns array -> $m[2]
		// cfg: third sub-patterns array  -> $m[3]
		foreach ($m[0] as $index => &$fullMatch)
		{
			$proc = $m[1][$index];
			$tpl  = $m[2][$index];
			$cfg  = $m[3][$index];
			
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
			$pObj->template_dir = $this->getDirectory( self::typeTpl   );
			$pObj->compile_dir  = $this->getDirectory( self::typeComp  );
			$pObj->config_dir   = $this->getDirectory( self::typeCfg   );
			$pObj->cache_dir    = $this->getDirectory( self::typeCache );

			// load configuration file (if any)
			if (! empty( $cfg ))
				$pObj->config_load( $this->getFilename($cfg, self::typeCfg) );

			// get the result
			$output = $pObj->fetch( $this->getFilename($tpl, self::typeTpl)  );
			
			// replace full match with output of processor
			$this->replaceMarker( $fullMatch, $output, $text );
			
		} // end foreach
	
		return true; // continue hook chain.
	}
/* ------------------------------------------------------------------
    SUPPORT METHODS                                                
   ------------------------------------------------------------------ */	
	
	private function replaceMarker( $m, $r, &$subject )
	{
		$subject = str_replace( $m, $r, $subject );
	}

	private function checkFile( $file, $type )
	{	return file_exists( $this->getFilename( $file, $type) ); }
	
	private function getDirectory( $type )
	{ 
		global $wgInstallDir;
		return $wgInstallDir.'/'.self::$base.self::$dirs[ $type ];
		#return $wgInstallDir; 
	}
	
	private function getExtension( $type )
	{	return self::$ext[ $type ]; }
	
	private function getFilename( $name, $type )
	{
		$dir = $this->getDirectory( $type );
		$ext = $this->getExtension( $type );
	
		return $dir.'/'.$name.'.'.$ext;
	}
	
} // END CLASS DEFINITION
?>