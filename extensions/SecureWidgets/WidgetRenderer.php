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

	const NAME = 'securewidgets-renderer';
	
	static $instance = null;
	
	public function __construct( ) {

		if ( self::$instance !== null )
			throw new Exception( __CLASS__.": singleton pattern" );
		self::$instance = $this;
		
		parent::__construct();
		
	}
	/**
	 * Singleton interface
	 */
	public static function gs() {
	
		return self::$instance;
	}
	/**
	 * @return $obj mixed String with result OR MessageList object instance in case of errors
	 */
	public static function render( &$widget, &$params ) {
	
		$code = $widget->getCode();
		
		// extract parameters from widget template
		$tp = WidgetParameters::newFromTemplate( $code );
		
		// prepare the input variables
		$ip = WidgetParameters::newFromParamList( $params );
		
		// Case 1: template does not have parameters
		//         Don't make waves even in the case where 
		//         input variables are provided where none are required...
		if ( $tp->isEmpty() )
			return $code;
			
		// Case 2: template has parameters but no input variables provided
		if ( $ip->isEmpty() ) {
			$msg = new MessageList;
			return $msg->pushMessageById( self::NAME . '-missing-inputs' );
		}
			
		// Case 3: template specifies parameter types and input variables do not match
		
		
		// Step 4: perform type checking
		
		
	}
	
}//end class definition
new MW_WidgetRenderer;

include 'WidgetRenderer.i18n.php';