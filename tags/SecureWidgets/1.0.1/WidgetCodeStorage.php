<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.1 
 * @Id $Id: WidgetCodeStorage.php 1109 2008-05-15 19:29:49Z jeanlou.dupont $
 */

abstract class MW_WidgetCodeStorage 
	extends ExtensionBaseClass {

	const VERSION = '1.0.1';
	const NAME    = 'securewidgets';
		
	/**
	 * Widget Name
	 */
	var $name = null;

	/**
	 * Constructor
	 */
	public function __construct( ) {

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