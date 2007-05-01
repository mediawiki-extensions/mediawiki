<?php
/*
 * ExtensionClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a toolkit for easier Mediawiki
 *           extension development.
 *
 * FEATURES:
 * - 'singleton' implementation suited for extensions that require single instance
 * - 'magic word' helper functionality
 * - limited pollution of global namespace
 *
 * Tested Compatibility: MW 1.8.2 (PHP5), 1.9.3
 *
 * History:
 * v1.0		Initial availability
 * v1.01    Small enhancement in processArgList
 * v1.02    Corrected minor bug
 * v1.1     Added function 'checkPageEditRestriction'
 * v1.2     Added 'getArticle' function
 * ----     Moved to SVN management
 * v1.3     Added wgExtensionCredits updating upon Special:Version viewing
 * v1.4     Fixed broken singleton functionality
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'ExtensionClass',
	'version' => 'v1.4 $LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

class ExtensionClass
{
	static $gObj; // singleton instance
	
	var $className;
	
	var $paramPassingStyle;
	var $ext_mgwords;	
	
	// Parameter passing style.
	const mw_style = 1;
	const tk_style = 2;
	
	public static function &singleton( $mwlist=null ,$globalObjName=null, $passingStyle = mw_style )
	{
		// Let's first extract the callee's classname
		$trace = debug_backtrace();
		$cname = $trace[1]['class'];

		// If no globalObjName was given, create a unique one.
		if ($globalObjName === null)
			$globalObjName = substr(create_function('',''), 1 );
		
		// Since there can only be one extension with a given child class name,
		// Let's store the $globalObjName in a static array.
		if (!isset(self::$gObj[$cname]) )
			self::$gObj[$cname] = $globalObjName; 
				
		if ( !isset( $GLOBALS[self::$gObj[$cname]] ) )
			$GLOBALS[$globalObjName] = new $cname( $mwlist, $passingStyle );
			
		return $GLOBALS[self::$gObj[$cname]];
	}
	public function ExtensionClass( $mgwords=null, $passingStyle = mw_style )
	/*
	 *  $mgwords: array of 'magic words' to subscribe to *if* required.
	 */
	{	
		$this->paramPassingStyle = $passingStyle;
		
		// Let's first extract the callee's classname
		$trace = debug_backtrace();
		$this->className= $cname = $trace[1]['class'];
		// And let's retrieve the global object's name
		$n = self::$gObj[$cname];
		
		global $wgExtensionFunctions;
		$wgExtensionFunctions[] = create_function('',"global $".$n."; $".$n."->setup();");
		$this->ext_mgwords = $mgwords;		
		if (is_array($this->ext_mgwords) )
		{ 
			global $wgHooks;
			$wgHooks['LanguageGetMagic'][] = array($this, 'getMagic');
		}

		// v1.3 feature
		if ( in_array( 'hUpdateExtensionCredits', get_class_methods($this->className) ) )
		{
			global $wgHooks;
			$wgHooks['SpecialVersionExtensionTypes'][] = array( &$this, 'hUpdateExtensionCredits' );				
		}
	}
	public function getParamPassingStyle() { return $this->passingStyle; }
	public function setup()
	{
		if (is_array($this->ext_mgwords))
			$this->setupMagic();
	}
	// ================== MAGIC WORD HELPER FUNCTIONS ===========================
	public function getMagic( &$magicwords, $langCode )
	{
		foreach($this->ext_mgwords as $index => $key)
			$magicwords [$key] = array( 0, $key );
		return true;
	}
	public function setupMagic( )	
	{
		global $wgParser;
		foreach($this->ext_mgwords as $index => $key)
			$wgParser->setFunctionHook( "$key", array( $this, "mg_$key" ) );
	}
	// ================== GENERAL PURPOSE HELPER FUNCTIONS ===========================
	public function processArgList( $list, $getridoffirstparam=false )
	/*
	 * The resulting list contains:
	 * - The parameters extracted by 'key=value' whereby (key => value) entries in the list
	 * - The parameters extracted by 'index' whereby ( index = > value) entries in the list
	 */
	{
		if ($getridoffirstparam)   
			array_shift( $list );
			
		// the parser sometimes includes a boggie
		// null parameter. get rid of it.
		if (count($list) >0 )
			if (empty( $list[count($list)-1] ))
				unset( $list[count($list)-1] );
		
		$result = array();
		foreach ($list as $index => $el )
		{
			$t = explode("=", $el);
			if (!isset($t[1])) 
				continue;
			$result[ "{$t[0]}" ] = $t[1];
			unset( $list[$index] );
		}
		if (empty($result)) 
			return $list;
		return array_merge( $result, $list );	
	}
	public function getParam( &$alist, $key, $index, $default )
	/*
	 *  Gets a parameter by 'key' if present
	 *  or fallback on getting the value by 'index' and
	 *  ultimately fallback on default if both previous attempts fail.
	 */
	{
		if (array_key_exists($key, $alist) )
			return $alist[$key];
		elseif (array_key_exists($index, $alist) )
			return $alist[$index];
		else
			return $default;
	}
	public function initParams( &$alist, &$templateElements )
	{
		foreach( $templateElements as $index => &$el )
			$alist[$el['key']] = $this->getParam( $alist, $el['key'], $el['index'], $el['default'] );
	}
	public function checkPageEditRestriction( &$title )
	// v1.1 feature
	// where $title is a Mediawiki Title class object instance
	{
		$proceed = false;
  
		$state = $title->getRestrictions('edit');
		foreach ($state as $index => $group )
			if ( $group == 'sysop' )
				$proceed = true;

		return $proceed;		
	} 
	public function getArticle( $article_title )
	{
		$title = Title::newFromText( $article_title );
		  
		// Can't load page if title is invalid.
		if ($title == null)	return null;
		$article = new Article($title);

		return $article;	
	}
}
?>