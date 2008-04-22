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
	 * Sets hooks
	 */
	public function _setup()
	{
		global $wgHooks;
		
		// scan the sub-class for all the methods
		// starting with 'on'
		$methods = get_class_methods( $this );
		
		if ( !empty( $methods ) )
			foreach( $methods as $method )
				if ( substr( $method, 0, 2 ) == 'on' )
					$wgHooks[ substr( $method, 2 ) ][] = array( $this, $method );
					
		// if the sub-class requires any additional setup time
		@$this->setup();
	}

} //end class

//</source>