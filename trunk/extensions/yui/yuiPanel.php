<?php
/*
 * yuiPanel.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides yui 'Panel' objects.
 * ========  
 *
 * Features:
 * *********
 *
 * DEPENDANCY:  'yui.php' base class
 * 
 * Tested Compatibility:  
 *
 * INSTALLATION NOTES:
 * -------------------
 * Add to LocalSettings.php
 *  require("extensions/yui/yuiPanel.php");
 *
 * History:
 * - v1.0
 *
 */

yuiPanelClass::singleton();

class yuiPanelClass extends yuiClass
{
	// constants.
	const thisName = 'yuiPanelClass';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version

	static $tags  = array( 'yuipanel' );
	
	// variables.
	var $panels; 
	var $done;
	var $placedJS;
	
	public static function &singleton() // required by ExtensionClass
	{
		#echo "yuiPanelClass::singleton\n"; 
		return parent::singleton( null, null, self::mw_style, 2 ); 
	}
	
	function yuiPanelClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 2)
	{
		#echo "yuiPanelClass::__construct\n";
		parent::__construct( null, self::mw_style, 2 );		// required by ExtensionClass

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.0 $LastChangedRevision$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Yahoo User Interface Panel class for Mediawiki '
		);
		
		$this->panels = array();
		$this->done = false;
		$this->placedJS = false;
	}
	public function setup() 
	{ 
		#echo "yuiPanelClass::setup\n";
		parent::setup();
		$this->setupTags( self::$tags );
		
		// these are the scripts we need from Yahoo.
		$l = array( 'yahoo', 'dom', 'event', 'container' );
		$this->addScript( $l );
	} 

/********************
	Tag handler
*********************/
	public function tag_yuipanel ( $input, $argv, &$parser )
	// <yuipanel> ... </yuipanel>
	{
		// if we get here, then that means we'll have to process
		// yui objects later on
		$i = count( $this->panels );
		$this->panels[] = "<div id='panel{$i}'>".$input."</div>";
		
		// positional marker
		return '___PANEL'.$i.'___';
	}

	public function hParserAfterTidy( &$parser, &$text )
	{
		parent::hParserAfterTidy( $parser, $text );
		
		// sometimes, the parser gets called more than once.
		#if ($this->done) return true;
		#$this->done = true;
	
		// replace our markets
		foreach( $this->panels as $index => $panel)
			$text = preg_replace( "/___PANEL{$index}___/" , $panel, $text );	
			
		// add the required JS code.
		if (!$this->placedJS)
		{
$text .= <<<EOT
<script language=javascript>
panel0 = new YAHOO.widget.Panel("panel0", { width:"300px", visible:true, constraintoviewport:false } );
panel0.render(); 
</script>		
EOT;
			$this->placedJS = true;
		}
		
		return true;
	}
} // END CLASS DEFINITION
?>