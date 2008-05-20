<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.1 
 * @Id $Id: TypeChecker.php 1114 2008-05-15 19:50:26Z jeanlou.dupont $
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
		
		'?'				=> array( 'call' => array( __CLASS__, 'isUnspecified' ),  'cast' => null ),
	
		'string'		=> array( 'call' => 'is_string' , 'cast' => 'strval' ),
		'integer'		=> array( 'call' => 'is_integer', 'cast' => 'intval'),	
		'float'			=> array( 'call' => 'is_float'  , 'cast' => 'floatval'  ),
	
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
		
			$results[ $param ] = self::checkParam( $targetType, $param );
		}
		
		return $results;
	}
	public static function checkParam( &$targetType, &$param ) {
	
		// can we understand the type at least?
		if ( !self::isSupported( $targetType ))
			return null;
			
		return self::doTypeCheck( $targetType, $param );
	}
	protected static function doTypeCheck( &$type, &$param ) {
	
		$entry  = self::$types[ $type ];
		$call   = $entry['call'];
		$cast   = $entry['cast'];
		
		if ( !is_callable( $call ) )
			throw new Exception( __METHOD__. ": type checking function/method must be callable ($type)" );
		
		$_param = $param;
		$cparam = $param;
		
		// type-casting trick...
		if ( !is_null( $cast ) ) {
			$cparam = call_user_func( $cast, $param );
		}
		#echo __METHOD__. ": cast: $cast -- cparam: $cparam \n<br/>";
		
		if ( strval( $cparam ) == strval( $param ) )
			return true;
		
		return call_user_func( $call, $param );
	}
	/**********************************************************
	 * 					PROTECTED interface
	 **********************************************************/

	protected static function isUnspecified( $type ) {
	
		return true;
	}
	
	protected static function callHooks() {
	
		if ( self::$hookCalled )
			return;
		self::$hookCalled = true;
		wfRunHooks( 'typechecker', array( &self::$types )  );
		
	}
}//end class definition