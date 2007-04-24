<?php
/*
 * AddScript.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Inserts <script> tags at the bottom of the page's head.
 *
 * Features:
 * *********
 * 
 * -- Local files only
 * -- Files must be located in wiki installation
 *    home directory/jsscripts
 *
 * -- <addscript src='local URL' />
 *    e.g. <addscript src=/sarissa/sarissa />
 *    e.g. {{#addscript: src=/sarissa/sarissa }}
 *    e.g. {{#addscript: /sarissa/sarissa }}
 *
 *    Results in /home/jsscripts/sarissa/sarissa.js
 *    being added to the page's head section <script> tags,
 *    provided the said file exists.
 *
 * -- {{#addscript:filename}}
 * -- {{#addscript:src=filename}}
 *
 * DEPENDANCY:  NONE
 * 
 * Tested Compatibility:  MW 1.8.2, 1.9.3
 *
 * History:
 * - v1.0
 * - v1.1    - Added 'magic word' interface
 *             (i.e. {{#addscript: 'script file name' }} )
 * - v1.2    - Fixed hook chaining (missing 'return true;' statement)
 *           - Address XSS vulnerability
 *           -- Strip all '.' from the URI hence '.js' extension
 *              cannot be supplied by user anymore and therefore
 *              extension appends it.
 *
 * - v1.3    - Changed hook method to make it easier on parser caching.
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'AddScript Extension', 
	'version' => '1.3',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

$wgExtensionFunctions[] = "wfAddScriptSetup";
$GLOBALS['asObj'] = new AddScriptClass;
$wgHooks['LanguageGetMagic'][]  = array( $asObj, 'hGetMagic' );

function wfAddScriptSetup( )
{
	global $asObj;
	global $wgHooks;

	$wgHooks['ParserAfterTidy'][] = array( $asObj, 'feedScripts' );	

	global $wgParser;
	$wgParser->setHook(         'addscript', array( &$asObj, 'pSet' ) );
	$wgParser->setFunctionHook( 'addscript', array( &$asObj, 'mg_pSet' ) );	
}

class AddScriptClass
{
	static $base = 'jsscripts/';
	
	static function getGlobalObjectName() { return "asObj";           }
	static function &getGlobalObject()    { return $GLOBALS['asObj']; }	

	var $slist;
	
	public function pSet( &$text, &$argv, &$parser)
	{
		$text = $this->processURI( $argv['src'] );
		return $text;
	}
	public function mg_pSet( &$parser, $uri )
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
	public function hGetMagic( &$magicWords, $langCode )
	{
		$magicWords['addscript'] = array( 0, 'addscript' );
		return true;		
	}
	public function feedScripts( &$parser, &$text )
	{
		global $wgScriptPath;
		
		if (!empty($this->slist))
			foreach($this->slist as $sc)
				$op->addScript('<script src="'.$wgScriptPath.'/'.self::$base.$sc.'.js" type="text/javascript"></script>');
				
		return true; // v1.2 fix
	}
	
} // END CLASS DEFINITION
?>