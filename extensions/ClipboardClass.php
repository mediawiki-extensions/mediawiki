<?php
/*
 * ClipboardClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a 'clipboard' for Mediawiki article titles.
 * ========  Especially useful for collecting candidate titles
 *           for exporting through 'Special:Export' page. 
 *
 * Features:
 * *********
 *
 * - Clipboard data storage provided through 'Cookie' functionality
 * - Javascript button to 'Add Title to Clipboard'
 * - Javascript button to 'Empty the Clipboard'
 * - Javascript button to 'Paste Clipboard to Edit area'
 * -- Supports the Article Edit Area
 * -- Supports the 'Export Pages Edit Area'
 *
 * - Automatically tries popular image extension types
 *   (e.g. png, gif, jpg, jpeg) for the icon images of the toolbox area 
 *
 * DEPENDANCY:  ExtensionClass.php & Clipboard.js
 * 
 * Tested Compatibility:  MW 1.8.2
 *
 * INSTALLATION NOTES:
 * -------------------
 * 1) Images must be named according to the following:
 *  Clipboard_add
 *  Clipboard_paste
 *  Clipboard_empty
 *  Clipboard_show
 * 
 * (image type supported: see Features section)
 *
 * E.g. upload an icon for the 'Clipboard_add' action using the
 *      'Clipboard_add.png' upload name (as example).
 *
 * 2) Usual LocalSettings.php configuration:
 *    require_once("extensions/ClipboardClass.php");
 *
 * 3) Clipboard.js file must located in '/jsscripts/' in
 *    the Mediawiki installation root.
 *
 * History:
 * - v1.0
 * - v1.01  - Missing 'global $wgScriptPath' ...
 * - v1.02  - Added extra protection for when hook '' is called multiple times.
 * - v1.03  - Adjusted a variable in order to resolve conflict with new
 *            'ExtensionClass' extension version.
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => 'Clipboard Extension', 
	'version' => 'v1.03 $LastChangedRevision$',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

ClipboardClass::singleton();

class ClipboardClass extends ExtensionClass
{
	// Constants.
	static $actions =         array( 'show', 'add', 'empty', 'paste',  );
	static $extList =         array( 'png', 'gif', 'jpg', 'jpeg' );
	static $JsHandlerScript = 'jsscripts/Clipboard.js';
	
	var $_scriptsAdded;
	
	public static function &singleton( ) // required by ExtensionClass
	{ return parent::singleton( ); }
	
	function ClipboardClass()
	{
		parent::__construct(); 			// required by ExtensionClass
		
		global $wgHooks;
		$wgHooks['MonoBookTemplateToolboxEnd'][] = array( $this, 'hToolboxEnd');
		$wgHooks['BeforePageDisplay'][]          = array( $this, 'hBeforePageDisplay' );
		
		$this->_scriptsAdded = false;
	}
	public function setup() { } // nothing special to do in this case.

	public function hToolboxEnd( $skin )
	/*
	 *  Hook used to:
	 *  - Add Clipboard images (if configured) to Toolbox
	 *    Adds Toolbox links (assuming 'monobook' skin nomenclature)
	 */
	{
		$allImg = true;
		foreach( self::$actions as $index => $name )
		{
			$img = $this->getImageURL("Clipboard_{$name}");
			$imgs[] = $img;
			// check if we have all the necessary images.
			if ($img == null) $allImg = false;
			
		}	
		// if we have all the necessary icons, just line them all on one <li>
		if ($allImg) echo "<li id='clipboard' >";
		foreach( self::$actions as $index => $name )
		{
			if (!$allImg) echo "<li id='clipboard-$name'>";
			
			// Insert Javascript hook
			$msg = $this->getMessage( $name );
			echo "<a title='{$msg}' href='#' onMouseDown='return mwClipboard.{$name}()' >";
			
			// Put a nice icon if we can
			$img = $imgs[$index];
			if ($img !== null) echo "<img src='{$img}' />";
			else               echo $msg;
				
			echo "</a>  ";  // put some space between icons
			if (! $allImg) echo "</li>\n";
		}
		if ($allImg) echo "</li>\n";
		
		return true;
	}
	public function hBeforePageDisplay( &$op )
	/*
	 *  Hook used to:
	 *  - Add the required 'head' script
	 */
	{
		if ($this->_scriptsAdded) return true;
		$this->_scriptsAdded = true;
		
		global $wgScriptPath; // v1.01
		$op->addScript('<script src="'.$wgScriptPath.'/'.self::$JsHandlerScript.'" type="text/javascript"></script>');
		return true;
	}
	
	private function getImageURL( $imgName )
	{
		foreach( self::$extList as $index => $ext )
			if ( ($r = $this->tryImageURL($imgName.".{$ext}")) !== null )
				return $r;
				
		return null;
	}
	private function tryImageURL( $imgName )
	{
		$image = Image::newFromName( $imgName );
		if (!$image->exists()) return null;
		
		return $image->getURL();
	}
	private function getMessage( &$msgId )
	/*
	 * TODO: internationalize...
	 */
	{
		static $msg = array(
			'add'   => 'Add Title',
			'empty' => 'Empty Clipboard',
			'paste' => 'Paste Clipboard',
			'show'  => 'Show Title List'
		);
		
		return $msg[ $msgId ];
	}
	
} // END CLASS DEFINITION
?>