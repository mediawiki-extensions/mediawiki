<?php
/*
 * AddScriptCss.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * 
 * Purpose:  Inserts <script> & <link> tags at the bottom of the page's head.
 *
 * Features:
 * *********
 * 
 * -- Local files only
 * -- Files must be located in wiki installation
 *    home directory/scripts
 *
 * Examples:
 * =========
 * -- <addscript src='local URL' />
 *    e.g. <addscript src=/sarissa/sarissa />
 *    e.g. {{#addscript: src=/sarissa/sarissa }}
 *    e.g. {{#addscript: /sarissa/sarissa }}
 *
 *    Results in /home/scripts/sarissa/sarissa.js
 *    being added to the page's head section <script> tags,
 *    provided the said file exists.
 *
 * Syntax:
 * =======
 * Form 1: <addscript src=filename [type={js|css}] [pos={head|body}] />
 *
 * Form 2: {{#addscript:src=filename [|type={js|css} [|pos={head|body}] }}
 *
 * If no 'type' field is present, then the extension
 * assumes 'js'.
 *
 * If no 'pos' field is present, then the extension
 * assumes 'body'
 *
 * DEPENDANCY:  ExtensionClass (>=v1.92)
 * 
 * Tested Compatibility:  MW 1.8.2, 1.10
 *
 * History:
 * - v1.0  Builds on existing 'AddScript' extension
 *
 * TODO:
 * =====
 * - adjust for 'autoloading'
 * - internationalize
 */

// Verify if 'ExtensionClass' is present.
if ( !class_exists('ExtensionClass') )
	echo 'ExtensionClass missing: AddScriptCss extension will not work!';	
else
	AddScriptCssClass::singleton();

class AddScriptCssClass extends ExtensionClass
{
	// constants.
	const thisName = 'AddScriptCss';
	const thisType = 'other';  

	// script types
	const type_js  = 1;
	const type_css = 2;
	
	// position types
	const pos_body = 1;
	const pos_head = 2;
	
	// error codes.
	const error_none     = 0;
	const error_uri      = 1;
	const error_bad_type = 2;
	const error_bad_pos  = 3;
		
	static $base = 'scripts/';

	static $mgwords = array( 'addscript' );

	public static function &singleton($mwlist, $globalObjName, $passingStyle , $depth ) // required by ExtensionClass
	{ return parent::singleton( $mwlist, $globalObjName, $passingStyle , $depth );	}
	
	function AddScriptCssClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 1 )
	{
		parent::__construct( self::$mgwords, $passingStyle, $depth );

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.00 $id$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Adds javascript and css scripts to the page HEAD '
		);

		// <addscript... />
		global $wgParser;
		$wgParser->setHook( 'addscript', array( &$this, 'pSet' ) );
	}
	public function setup() 
	{ parent::setup();	} 

	var $slist;

	public function pSet( &$text, &$params, &$parser)
	{ 
		return $this->process( $params ); 
	}
	
	public function mg_addscript( $params )
	{
		$params = $this->processArgList( $params, true );		
		return $this->process( $params );
	}
	private function setupParams( &$params )
	{
		$template = array(
			array( 'key' => 'src',  'index' => '0', 'default' => '' ),
			array( 'key' => 'type', 'index' => '1', 'default' => 'js' ),
			array( 'key' => 'pos',  'index' => '2', 'default' => 'body' ),
			#array( 'key' => '', 'index' => '', 'default' => '' ),
		);
		parent::initParams( $params, $template, true );
	}
	private function normalizeParams( &$params )
	{
		// This function checks the validity of the following
		// parameters: 'type' and 'pos'
		extract( $params );
		
		$type=strtolower( $type );
		if ( ($type!='js') && ($type!='css') )
			return self::error_bad_type;

		$pos=strtolower( $pos );
		if ( ($pos!='head') && ($pos!='body') )
			return self::error_bad_pos;

		return self::error_none;		
	}
	private function process( &$params )
	{
		$this->setupParams( $params );

		$errCode = self::error_none;
		$r = $this->normalizeParams( $params );
		if ($r!=self::error_none) return $this->errMessage( $r );

		extract( $params );
		// src, type, pos
		
		$src = $this->cleanURI( $src, $type );
		if (!$this->checkURI( $src, $type ))
			return $this->errMessage( self::error_uri ); 

		global $wgScriptPath;
		$p = $wgScriptPath.'/'.self::$base.$src.'.'.$type;

		switch( $type )
		{
			case 'css':
				$t = '<link href="'.$p.'" rel="stylesheet" type="text/css" />';
				break;		
			default:
			case 'js':
				$t = '<script src="'.$p.'" type="text/javascript"></script>';
				break;
		}	

		$this->addHeadScript( $t );

		// everything OK
		return null;
	}
	private function cleanURI( $uri )
	{
		return str_replace( array('/../', '../', '\\..\\',
										 "..\\",'"','`','&','?',
										 '<','>','.' ), "", $uri);
	}
	private function checkURI( $uri, $type )
	{
		// uri must resolved to a local file in the $base directory.
		$spath = self::$base.$uri.'.'.$type;
		
		// we need the full path on windows system...
		$cpath = dirname(__FILE__);
		// assume the script path is one level up
		// i.e. this file should be in '/extensions' but the js script
		// directory should be in the base.
		$bpath = dirname( $cpath );
		
		return file_exists( $bpath."/{$spath}" );
	} 
	private function errMessage( $errCode )
	{
		static $m = array(
			self::error_none => 'no error',
			self::error_uri:
			self::error_bad_type:
			self::error_bad_pos:

		);
		
		 invalid URI  <i><b>'.$uri.'</b></i><br/>'; //FIXME
		
		$message = 'AddScriptCss: '.$m[ $errCode ];
	
	}
	
} // END CLASS DEFINITION
?>