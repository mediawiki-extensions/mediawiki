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

	static $types = array(
		
		'string'		=> array( 'f' => 'is_string' ),
		'integer'		=> array( 'f' => 'is_string' ),	
		'float'			=> array( 'f' => 'is_float'  ),
		'scalar'		=> array( 'f' => 'is_scalar' ),
		'numeric'		=> array( 'f' => 'is_numeric'),
	
	);

	/**********************************************************
	 * 					PUBLIC interface
	 **********************************************************/
	
	public static function isSupported( $type ) {

		self::$callHooks();
		
		return ( array_key_exists( $type, self::$types ) );
	}

	public static function check( &$targetType, &$param ) {
	
		
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