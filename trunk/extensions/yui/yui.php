<?php
/*
 * yui.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a base for Yahoo User Interface components.
 * ========  
 *
 * Features:
 * *********
 *
 * DEPENDANCY:  ExtensionClass extension (>v1.7)
 * 
 * Tested Compatibility:  
 *
 * INSTALLATION NOTES:
 * -------------------
 * Add to LocalSettings.php
 *  require("extensions/ExtensionClass.php");
 *  require("extensions/yui/yui.php");
 *
 * History:
 * - v1.0
 *
 */

class yuiClass extends ExtensionClass
{
	// constants.
	const thisName = 'yuiClass';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version

	var cssURI = array(
	'reset'       => 'http://yui.yahooapis.com/2.2.2/build/reset/reset-min.css',
	'fonts'       => 'http://yui.yahooapis.com/2.2.2/build/fonts/fonts-min.css',
	'grids'       => 'http://yui.yahooapis.com/2.2.2/build/grids/grids-min.css',	
	'button'      => 'http://yui.yahooapis.com/2.2.2/build/button/assets/button.css',	
	'calendar'    => 'http://yui.yahooapis.com/2.2.2/build/calendar/assets/calendar.css',	
	'container'   => 'http://yui.yahooapis.com/2.2.2/build/container/assets/container.css',
	'datatable'   => 'http://yui.yahooapis.com/2.2.2/build/datatable/assets/datatable.css',
	'logger'      => 'http://yui.yahooapis.com/2.2.2/build/logger/assets/logger.css',
	'menu'        => 'http://yui.yahooapis.com/2.2.2/build/menu/assets/menu.css',	
	'tabview'     => 'http://yui.yahooapis.com/2.2.2/build/tabview/assets/tabview.css',
	'border_tabs' => 'http://yui.yahooapis.com/2.2.2/build/tabview/assets/border_tabs.css',
	'tree'        => 'http://yui.yahooapis.com/2.2.2/build/treeview/assets/tree.css',
	); 

	var jsURI = array(
	/* Utilities (also aggregated in yahoo-dom-event.js and utilities.js) */ 
	'yahoo'      => "http://yui.yahooapis.com/2.2.2/build/yahoo/yahoo-min.js", 
	'dom'        => "http://yui.yahooapis.com/2.2.2/build/dom/dom-min.js", 
	'event'      => "http://yui.yahooapis.com/2.2.2/build/event/event-min.js", 
	'element'    => "http://yui.yahooapis.com/2.2.2/build/element/element-beta-min.js", 
	'animation'  => "http://yui.yahooapis.com/2.2.2/build/animation/animation-min.js", 
	'connection' => "http://yui.yahooapis.com/2.2.2/build/connection/connection-min.js", 
	'datasource' => "http://yui.yahooapis.com/2.2.2/build/datasource/datasource-beta-min.js", 
	'dragdrop'   => "http://yui.yahooapis.com/2.2.2/build/dragdrop/dragdrop-min.js", 
	'history'    => "http://yui.yahooapis.com/2.2.2/build/history/history-experimental-min.js",
	/* Controls */
	'autocomplete' => "http://yui.yahooapis.com/2.2.2/build/autocomplete/autocomplete-min.js", 
	'button'       => "http://yui.yahooapis.com/2.2.2/build/button/button-beta-min.js", 
	'calendar'     => "http://yui.yahooapis.com/2.2.2/build/calendar/calendar-min.js", 
	'container'    => "http://yui.yahooapis.com/2.2.2/build/container/container-min.js", 
	'datatable'    => "http://yui.yahooapis.com/2.2.2/build/datatable/datatable-beta-min.js", 
	'logger'       => "http://yui.yahooapis.com/2.2.2/build/logger/logger-min.js", 
	'menu'         => "http://yui.yahooapis.com/2.2.2/build/menu/menu-min.js", 
	'slider'       => "http://yui.yahooapis.com/2.2.2/build/slider/slider-min.js", 
	'tabview'      => "http://yui.yahooapis.com/2.2.2/build/tabview/tabview-min.js", 
	'treeview'     => "http://yui.yahooapis.com/2.2.2/build/treeview/treeview-min.js", 	
	);

	// variables.
	
	public static function &singleton($mwlist, $globalObjName, $passingStyle , $depth ) // required by ExtensionClass
	{ return parent::singleton( $mwlist, $globalObjName, $passingStyle , $depth ); }
	
	function yuiClass( $mgwords, $passingStyle )
	{
		parent::__construct( $mgwords, $passingStyle ); 			// required by ExtensionClass

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.0 $LastChangedRevision: 115 $',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'Yahoo User Interface base class for Mediawiki '
		);
	}
	public function setup() { parent::setup(); } 


} // END CLASS DEFINITION
?>