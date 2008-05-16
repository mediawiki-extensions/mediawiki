<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.0 
 * @Id $Id$
 */

class MW_WidgetFactory
	extends ExtensionBaseClass {

	const NAME = 'securewidgets-factory';
	
	/**
	 * Code store list
	 */
	var $codeStores = array();

	/**
	 * Singleton instance reference
	 */
	private static $instance = null;
	
	var $fetchedOtherStores = false;
	
	/**
	 * Constructor
	 */
	public function __construct() {
	
		if ( self::$instance !== null )
			throw new Exception( __CLASS__. ": there can only be one instance of this class" );
			
		self::$instance = $this;

		parent::__construct();		
		
		$this->registerDefaultStorages();
	}
	
	public function setup() {
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
	public function newFromWidgetName( &$name ) {
	
		$this->fetchOtherStores();
	
		$msgs = new MessageList;
	
		foreach( $this->codeStore as $store ) {
		
			$store->setName( $name );
			$rawCode = $store->getCode();
			if ( is_string( $rawCode ) ) {
			
				$code = self::extractCode( $rawCode );
				if ( $code === false ) {
					$msgs->pushMessageById( self::NAME . '-nocode' );
					continue;
				}
				return new Widget( $name, $code );
			}
			else
				$msgs->insertMessages( $rawCode );
		} //foreach
		
		// error
		return $msgs;
	
	}
	/****************************************************************
	 * 							PROTECTED
	 ****************************************************************/
	/**
	 * Just pick up the code between in the <includeonly> tag section
	 */
	static $codeSectionPattern = '/\<includeonly\>(.*)\<\/includeonly\>/siU';
	protected static function extractCode( &$code ) {
	
		$result = preg_match( self::$codeSectionPattern, $code, $match );
		if ( $result === false )
			return false;
			
		return $match[1];
	}
	/**
	 * Uses a 'hook' to look around if other extensions are capable
	 * of providing 'storage' capabilities. 
	 */
	protected function fetchOtherStores() {
	
		// just do this once
		if ( $this->fetchedOtherStores )
			return;
		$this->fetchedOtherStores = true;
		
		wfRunHooks( 'widget_register_storage', array( &$this->codeStore ) );
	}
	/**
	 * Must be placed in order of priority with
	 * regards to searching locations.
	 */
	protected function registerDefaultStorages( ) {
	
		$this->codeStore[] = MW_WidgetCodeStorage_Database::gs();
		$this->codeStore[] = MW_WidgetCodeStorage_Repository::gs();		
		return $this;
	
	}

} //Widget: end class definition

new MW_WidgetFactory;
include 'WidgetFactory.i18n.php';
