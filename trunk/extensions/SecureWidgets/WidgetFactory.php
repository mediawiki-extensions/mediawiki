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
#include "MessageList.php";

class MW_WidgetFactory
	extends ExtensionBaseClass {

	/**
	 * Code store list
	 */
	var $codeStores = array();

	/**
	 * Singleton instance reference
	 */
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
	/**
	 * Go through all registered code store
	 * 
	 * @param $name string
	 * @return $obj mixed Widget / MW_SecureWidgetsMessageList
	 */
	public function newWidgetFromName( &$name ) {
	
		$msgs = new MessageList;
	
		foreach( $this->codeStore as $store ) {
		
			$store->setName( $name );
			$code = $store->getCode();
			if ( $code !== null )
				return new Widget( $name, $code );
			
			$msgs->pushMessages( $store->getLastErrorMessages() );
		}
	
		// error
		return $msgs;
	
	}
	/****************************************************************
	 * 							PROTECTED
	 ****************************************************************/
	
	/**
	 * Must be placed in order of priority with
	 * regards to searching locations.
	 */
	protected function registerDefaultStorages( ) {
	
		$this->codeStore[] = MW_WidgetCodeStorage_Database::gs();
		$this->codeStore[] = MW_WidgetCodeStorage_Repository::gs();		
		return $this;
	
	}
	/****************************************************************
	 * 							HOOKS
	 ****************************************************************/
	/**
	 * HOOK: provides a facility to add code storage locations
	 * 
	 * @param $object MW_WidgetCodeStorage class
	 */
	public function hook_widget_register_storage( &$store ) {
	
		$this->codeStore[] = $store;
		
		return true;
	}
	

} //Widget: end class definition

new MW_WidgetFactory;