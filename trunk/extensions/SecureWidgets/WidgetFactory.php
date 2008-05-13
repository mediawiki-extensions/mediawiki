<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

include "WidgetCodeStorage.php";
include "WidgetCodeStorage_Database.php";
include "WidgetCodeStorage_Repository.php";

class WidgetFactory
	extends ExtensionBaseClass {

	/**
	 * Code store list
	 */
	var $codeStores = array();

	private static $instance = null;
	
	/**
	 * Constructor
	 */
	public function __construct() {
	
		if ( self::$instance !== null )
			throw new Exception( __CLASS__. ": there can only be one instance of this class" );
			
		self::$instance = $this;
		
		$this->registerDefaultStorages();
	}
	/****************************************************************
	 *							PUBLIC 
	 ****************************************************************/
	
	/**
	 * Returns the singleton instance of this class
	 * 
	 * @return Object
	 */
	public static function gs() {
	
		return self::$instance;
	}
	
	public function newWidgetFromName( &$name ) {
	
		foreach( $this->codeStore as $store ) {
		
			$store->setName( $name );
			$code = $store->getCode();
			if ( $code !== null )
				return new Widget( $name, $code );
			
			$this->msgList[] = $store->getLastErrorMessages();
		}
	
		// error
		return false;
	
	}
	/****************************************************************
	 * 							PROTECTED
	 ****************************************************************/
	
	/**
	 * Must be placed in order of priority with
	 * regards to searching locations.
	 */
	protected function registerDefaultStorages( ) {
	
		$this->codeStore[] = new MW_WidgetCodeStorage_Database;
		$this->codeStore[] = new MW_WidgetCodeStorage_Repository;		
		return $this;
	
	}
	/****************************************************************
	 * 							HOOKS
	 ****************************************************************/
	/**
	 * HOOK
	 */
	public function hook_widget_register_storage( ) {
	
	}
	

} //Widget: end class definition

new WidgetFactory;