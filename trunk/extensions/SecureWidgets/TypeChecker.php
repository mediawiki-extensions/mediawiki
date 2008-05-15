<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class TypeChecker {

	static $hookCalled = false;

	/**
	 * Callable entry:  (aka 'callback')
	 *   1) string with function name i.e. global function
	 *   2) array( class-name, method-name )
	 *   3) array( object-instance, method-name )
	 */
	static $types = array(
		
		'string'		=> array( 'is_string' ),
		'integer'		=> array( 'is_integer' ),	
		'float'			=> array( 'is_float'  ),
		'scalar'		=> array( 'is_scalar' ),
		'numeric'		=> array( 'is_numeric'),
	
	);

	/**********************************************************
	 * 					PUBLIC interface
	 **********************************************************/
	
	public static function isSupported( $type ) {

		self::callHooks();
		
		return ( array_key_exists( $type, self::$types ) );
	}
	/**
	 * INPUT:  ( param => target-type )
	 * OUTPUT: ( param => boolean-result )
	 */
	public static function checkParamList( $liste ) {
	
		if (empty( $liste ))
			return null;
			
		$results = array();
			
		foreach( $liste as $param => &$targetType ) {
		
			$results[ $param ] self::checkParam( $targetType, $param );
		}
		
		return $results;
	}
	public static function checkParam( &$targetType, &$param ) {
	
		// can we understand the type at least?
		if ( self::isSupported( $targetType ))
			return null;
			
		return self::doTypeCheck( $targetType, $param );
	}
	protected static function doTypeCheck( &$type, &$param ) {
	
		$callable  = self::$types[ $type ];
		if ( !is_callable( $callable ) )
			throw new Exception( __METHOD__. ": type checking function/method must be callable ($type)" );
					
		return call_user_func( $callable, $param );
	}
	/**********************************************************
	 * 					PROTECTED interface
	 **********************************************************/
	
	protected static function callHooks() {
	
		if ( self::$hookCalled )
			return;
		self::$hookCalled = true;
		wfRunHooks( 'typechecker', array( &self::$types )  );
		
	}
}//end class definition