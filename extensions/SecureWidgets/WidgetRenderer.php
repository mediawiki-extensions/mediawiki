<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class MW_WidgetRenderer 
	extends ExtensionBaseClass {

	public function __construct( ) {

		if ( self::$instance !== null )
			throw new Exception( __CLASS__.": singleton pattern" );
		self::$instance = $this;
		
		parent::__construct();
		
	}
	public static function gs() {
	
		return self::$instance;
	}
	/**
	 * @return $obj mixed String with result OR MessageList object instance in case of errors
	 */
	public static function render( &$widget, &$params ) {
	
		// extract parameters from widget template
		$tp = WidgetParameters::newFromTemplate( $widget->getCode() );
		
		// prepare the input variables
		$ip = WidgetParameters::newFromParamList( $params );
		
		// Case 1: template has parameters but no input variables provided
		// Case 2: template specifies parameter types and input variables do not match
		
	}
	
}//end class definition
new MW_WidgetRenderer;

include 'WidgetRenderer.i18n.php';