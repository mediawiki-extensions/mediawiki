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

	static $tags   = array( 'yuipanel' );
	static $defaults = array(
		array( 'key' => 'close',              'format' => 'bool',  'default' => 'true', 'index' => null ),
		array( 'key' => 'draggable',          'format' => 'bool',  'default' => 'true', 'index' => null  ),
		array( 'key' => 'modal',              'format' => 'bool',  'default' => 'false', 'index' => null  ),
		array( 'key' => 'visible',            'format' => 'bool',  'default' => 'true', 'index' => null  ),
		array( 'key' => 'x',                  'format' => 'int',   'default' => 'null', 'index' => null  ),
		array( 'key' => 'y',                  'format' => 'int',   'default' => 'null', 'index' => null  ),
		array( 'key' => 'fixedcenter',        'format' => 'bool',  'default' => 'false', 'index' => null  ),
		array( 'key' => 'width',              'format' => 'string','default' => '300px', 'index' => null  ),
		array( 'key' => 'height',             'format' => 'string','default' => '100px', 'index' => null  ),
		array( 'key' => 'zIndex',             'format' => 'int',   'default' => '0', 'index' => null  ),
		array( 'key' => 'constraintoviewport','format' => 'bool',  'default' => 'false', 'index' => null  ),
		array( 'key' => 'underlay',           'format' => 'string','default' => 'shadow', 'index' => null  ),
	);
	
	// variables.
	var $panels;
	var $configs;
	var $done;
	var $placedJS;
	
	public static function &singleton() // required by ExtensionClass
	{ return parent::singleton( null, null, self::mw_style, 2 ); }
	
	function yuiPanelClass( $mgwords = null, $passingStyle = self::mw_style, $depth = 2)
	{
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
		$this->configs = array();
		$this->done = false;
		$this->placedJS = false;
	}
	public function setup() 
	{ 
		parent::setup();
		$this->setupTags( self::$tags );
		
		// these are the scripts we need from Yahoo.
		$l = array( 'yahoo', 'dom', 'event', 'dragdrop', 'container' );
		$this->addScript( $l );
		
		// and the css stylesheets.
		$c = array( 'container' );
		$this->addStyle( $c );
	} 

/********************
	Tag handler
*********************/
	public function tag_yuipanel ( $input, $argv, &$parser )
	// <yuipanel parameters > ... </yuipanel>
	{
		// if we get here, then that means we'll have to process
		// yui objects later on
		$i = count( $this->panels );
		$this->panels[]  = "<div id='panel{$i}'>".$input."</div>";

		// input parameters are in an array with the following form:
		// 'key' = 'value'
		$this->configs[] = $argv; 
		
		// positional marker
		return '___PANEL'.$i.'___';
	}

	public function hParserAfterTidy( &$parser, &$text )
	{
		if (empty($this->panels))
			return true;

		parent::hParserAfterTidy( $parser, $text );
		
		// replace our markets
		foreach( $this->panels as $index => $panel)
			$text = preg_replace( "/___PANEL{$index}___/" , $panel, $text );	
		
		// Sometimes this hook is called more than once: make sure
		// we only include the JS code once!
		if (!$this->placedJS)
		{
$text .= <<<EOT
		<script type="text/javascript"> 
		function initPanels() 
		{
EOT;
		foreach( $this->configs as $index => $cfg )
			{
				$this->initParams( $cfg, self::$defaults );
				$this->formatParams( $cfg, self::$defaults );
		
				$l = $this->formatCfgLine( $cfg, self::$defaults );

$text .= <<<EOT
	panel{$index} = new YAHOO.widget.Panel('panel{$index}', { {$l} } );
	panel{$index}.render();
	panel{$index}.show();
	
EOT;
			}

$text .= <<<EOT
		} 
		YAHOO.util.Event.onDOMReady(initPanels); 
		</script>		
EOT;
			$this->placedJS = true;
		}
		
		return true;
	}
	
	function formatCfgLine( &$cfg, &$template )
	{
		$r = '';
		$last  = count ( $cfg );
		$index = 1;
		foreach( $cfg as $key => $value )
		{
			// get format of the key
			$f = $this->getFormat( $key, $template );
			
			if ($f=='string')
				$r .= $key.":"."'$value'";
			else
				$r .= $key.":".$value;
				
			if ($index++!=$last) $r.=", ";
		}
		return $r;	
	}
	
	
} // END CLASS DEFINITION
?>