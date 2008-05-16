<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.0 
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
	public function setup() {
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
	
		// error stack object
		$msgs = new MessageList();
	
		$code = $widget->getCode();
		$name = $widget->getName();
		
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
			return $msg->pushMessageById( self::NAME . '-missing-inputs', array( $name ) );
		}
			
		// Case 3: template specifies parameter types and input variables do not match
		foreach( $tp as $index => $e ) {
			
			if ( !isset( $e['n'] ))
				throw new Exception( __METHOD__.": name parameter missing in template");
			if ( !isset( $e['t'] ))
				throw new Exception( __METHOD__.": type parameter missing in template");
			
			$patt = $e['r'];
			$name = $e['n'];
			$type = $e['t'];
			$value = null;
			
			// make sure we have an input variable that corresponds
			// the a required template parameter
			if ( isset( $ip[ $name ] )) {
			
				$value = $ip[ $name ]['v'];
				
			} else {
				// is there a default value then??
				if ( isset( $e['d'] ) ) {
					$value = $e['d'];
				} else {
					$msgs->pushMessageById( self::NAME . '-missing-input', array( $name, $type ) );
					continue;
				}
			}
			
			$result = TypeChecker::checkParam( $type, $value );
			if ( $result === null ) {
				$msgs->pushMessageById( self::NAME . '-unsupported-type', array( $name, $type ) );
				continue;
			}
			if ( $result === false ) {
				$msgs->pushMessageById( self::NAME . '-type-mismatch', array( $name, $type ) );
				continue;
			}
		
			// everything looks OK - add value to template parameters list
			$tp->setParam( $index, 'v', $value);
			
		}//foreach
		
		// if we have error messages, exit now
		if ( !$msgs->isEmpty() )
			return $msgs;
		
		// Perform replacements in template and return the resulting code
		return WidgetParameters::doReplacementsInWidgetTemplate( $widget, $tp );
	}
	
}//end class definition
new MW_WidgetRenderer;

include 'WidgetRenderer.i18n.php';