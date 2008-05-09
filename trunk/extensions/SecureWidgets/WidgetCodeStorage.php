<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

abstract class WidgetCodeStorage 
	extends ExtensionBaseClass {

	/**
	 * Widget Name
	 */
	var $name = null;

	public function __construct( &$name ) {
	
		$this->name = $name;
	}

	abstract public function get();

	abstract public function getError();
	
}