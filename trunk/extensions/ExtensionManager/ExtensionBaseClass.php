<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 * @category ExtensionManager
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>

abstract class ExtensionBaseClass
{
	static $_ptag = 'ptag';
	static $_pfnc = 'pfnc';
	
	/**
	 * Base constructor
	 * If the sub-class defines a constructor,
	 * then the parent class constructor (this one here)
	 * must be called _first_.
	 */
	public function __construct() {
	
		// Register the extension so that it gets
		// initialized in the correct sequence
		global $wgExtensionFunctions;
		$wgExtensionFunctions[] = array( $this, '_setup' );
			
	}
	/**
	 * Setup of the extension's
	 * - Hooks
	 * - Parser Tags
	 * - Parser Functions
	 * - Custom setup
	 */
	public function _setup()
	{
		$methods = get_class_methods( $this );
		
		$this->setupHooks( $methods );
		$this->setupTags( $methods );
		$this->setupParserFunctions( $methods );
		
		// if the sub-class requires any additional setup time
		@$this->setup();
	}
	/**
	 * Sets hooks
	 * @param $methods array of class methods
	 */
	private function setupHooks( &$methods ){

		global $wgHooks;
		
		// scan the sub-class for all the methods
		// starting with 'on'
		if ( !empty( $methods ) )
			foreach( $methods as $method )
				if ( substr( $method, 0, 2 ) == 'on' )
					$wgHooks[ substr( $method, 2 ) ][] = array( $this, $method );
		
	}
	/** 
	 * Sets the parser 'tags'
	 * @param $methods array of class methods 
	 */
	private function setupTags( &$methods ){

		global $wgParser;
		
		$len = strlen( self::$_ptag );		
		
		if ( !empty( $methods ))
			foreach( $methods as $method )
				if ( substr( $method, 0, $len ) == self::$_ptag ) {
					$key = substr( $method, $len );
					$wgParser->setHook( "$key", array( $this, self::$_ptag.$key ) );
				}
		
	}
	/** 
	 * Sets the parser functions
	 * @param $methods array of class methods 
	 */
	private function setupParserFunctions( &$methods ){

		global $wgParser;
		
		$len = strlen( self::$_pfnc );		
		
		if ( !empty( $methods ))
			foreach( $methods as $method )
				if ( substr( $method, 0, $len ) == self::$_pfnc ) {
					$key = substr( $method, $len );
					$wgParser->setFunctionHook( "$key", array( $this, self::$_pfnc.$key ) );
				}
		
	}
	
	/** 
	 * Returns the name of the extension
	 * i.e. the class name with the optional leading MW_ string
	 */
	protected function getName(){

		$name = get_class( $this );
		return str_replace( 'MW_', '', $name );
	}
} //end class

//</source>