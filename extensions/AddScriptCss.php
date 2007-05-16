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
 * -- <addscript src=filename />
 * -- <addscript src=filename type=[js|css] />
 *
 * -- {{#addscript:filename}}
 * -- {{#addscript:src=filename}}
 * -- {{#addscript:src=filename|type=[js|css]}}
 *
 * If no 'type' field is present, then the extension
 * assumes '.js'.
 *
 * DEPENDANCY:  ExtensionClass (>=v1.9)
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

AddScriptCssClass::singleton();

class AddScriptCssClass extends ExtensionClass
{
	// constants.
	const thisName = 'AddScriptCss';
	const thisType = 'other';  

	// script types
	const type_js  = 1;
	const type_css = 2;
	
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

	public function pSet( &$text, &$argv, &$parser)
	{
		$text = $this->processURI( $argv['src'] );
		return $text;
	}
	public function mg_addscript( &$parser, $uri )
	{
		$e = explode( '=', $uri );
		if ( count($e) > 1)
		{ 
			if ($e[0] != 'src')
				return;
			$uri = $e[1];				
		}
		return $this->processURI( $uri );
	}
	private function processURI( $uri )
	{
		$uri = $this->cleanURI( $uri );
		if ($this->checkURI( $uri ))
		{
			$this->slist[] = $uri;
			return;
		}
		return 'addscript: invalid uri   <i><b>'.$uri.'</b></i><br/>';
	}
	private function cleanURI( $uri )
	{
		// v1.2: address XSS vulnerability
		$clean_uri = str_replace( array('/../', '../', '\\..\\',
										 "..\\",'"','`','&','?',
										 '<','>','.' ), "", $uri);
		return $clean_uri;
	}
	private function checkURI( $uri )
	{
		// uri must resolved to a local file in the $base directory.
		$spath = self::$base.$uri.'.js';
		
		// we need the full path on windows system...
		$cpath = dirname(__FILE__);
		// assume the script path is one level up
		// i.e. this file should be in '/extensions' but the js script
		// directory should be in the base.
		$bpath = dirname( $cpath );
		
		return file_exists( $bpath."/{$spath}" );
	} 

	public function feedScripts( &$parser, &$text )
	{
		global $wgScriptPath;
		global $wgOut;
		
		if (!empty($this->slist))
			foreach($this->slist as $sc)
				$wgOut->addScript('<script src="'.$wgScriptPath.'/'.self::$base.$sc.'.js" type="text/javascript"></script>');
				
		return true; // v1.2 fix
	}
	
} // END CLASS DEFINITION
?>