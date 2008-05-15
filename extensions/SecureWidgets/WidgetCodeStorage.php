<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

abstract class MW_WidgetCodeStorage 
	extends ExtensionBaseClass {

	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets';
		
	/**
	 * Widget Name
	 */
	var $name = null;

	/**
	 * Error list
	 */
	var $msgs = null;
	
	/**
	 * Constructor
	 */
	public function __construct( ) {
	
		$this->msgs = new MessageList();
		parent::__construct();
	}
	
	public function setName( &$name ) {
	
		$this->name = $name;	
	}
	/**
	 * Retrieve the code from the storage
	 */
	abstract public function getCode();

}