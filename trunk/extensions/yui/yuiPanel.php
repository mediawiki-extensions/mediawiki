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

	const tags  = array( 'yuipanel' );
	
	// variables.
	var $panels; 
	
	public static function &singleton() // required by ExtensionClass
	{ return parent::singleton( null, null, self::mw_style, 2 ); }
	
	function yuiPanelClass()
	{
		parent::__construct( ); 			// required by ExtensionClass

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.0 $LastChangedRevision$',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Yahoo User Interface Panel class for Mediawiki '
		);
		
		$this->panels = array();
	}
	public function setup() 
	{ 
		parent::setup();
		$this->setupTags( self::tags );
		
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
		
		// replace our markets
		foreach( $this->panels as $index => $panel)
		{
			$p = "/___PANEL{$index}___/";
			$text = preg_replace( $p, $panel, $text );	
		}
			
		// add the required JS code.
$text .= <<<EOT
<script language=javascript>
YAHOO.example.container.panel1 = new YAHOO.widget.Panel("panel0", { width:"300px", visible:true, constraintoviewport:false } ); 
</script>		
EOT;
		
		return true;
	}
} // END CLASS DEFINITION
?>