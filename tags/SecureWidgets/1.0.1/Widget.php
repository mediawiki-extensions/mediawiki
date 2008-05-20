<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.1 
 * @Id $Id: Widget.php 1092 2008-05-14 20:02:11Z jeanlou.dupont $
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