<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.0 
 * @Id $Id$
 */

class Widget {

	/**
	 * Widget name (unique identifier)
	 */
	var $name = null;

	/**
	 * Widget Version information
	 */
	var $version = null;

	/**
	 * Code (e.g. HTML/css/js)
	 */
	var $code   = null;
	
	/**
	 * Input parameters
	 */
	var $params = array();

	/**
	 * Constructor
	 */
	public function __construct( $name, $code ) {
	
		$this->name    = $name;
		$this->code    = $code;
		
	}
	
	public function getCode() {
	
		return $this->code;
	}
	public function getName() {
	
		return $this->name;
	}
} //Widget: end class definition