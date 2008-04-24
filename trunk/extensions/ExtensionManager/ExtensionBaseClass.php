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
	 * Method Prefixes
	 */
	static $_hook = 'hook';
	static $_ptag = 'ptag';
	static $_pfnc = 'pfnc';
	
	/**
	 * i18n messages
	 */
	static $msg = array();
	
	/**
	 * State constants
	 * NOTE: same as Extension:StubManager
	 */
	const STATE_OK        = 0;	
	const STATE_ERROR     = 1;
	const STATE_ATTENTION = 2;
	const STATE_DISABLED  = 3;
	
	static $state_icons = array(
		self::STATE_OK			=> 'icon_ok.png',
		self::STATE_ERROR		=> 'icon_error.png',
		self::STATE_ATTENTION	=> 'icon_attention.png',
		self::STATE_DISABLED	=> 'icon_disabled.png',
	);
	
	static $state_messages = array(
		self::STATE_OK			=> 'ok',
		self::STATE_ERROR		=> 'error',
		self::STATE_ATTENTION	=> 'attention required',
		self::STATE_DISABLED	=> 'disabled',
	);
	
	/**
	 * Status of this extension
	 */
	var $status = null;
	
	
	
	// ======================================================================
	// 									PUBLIC
	// ======================================================================	
	
	
	
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
	 * Setup of the extension's :
	 * - Hooks
	 * - Parser Tags
	 * - Parser Functions
	 * - Custom setup
	 * 
	 * @return void
	 */
	public function _setup()
	{
		$methods = get_class_methods( $this );
		
		$this->setupMessages( );
		$this->setupHooks( $methods );
		$this->setupTags( $methods );
		$this->setupParserFunctions( $methods );
		
		// if the sub-class requires any additional setup time
		@$this->setup();
		
		$this->doRegistration();
	}
	/**
	 * Returns the current state of this extension
	 * @return $state mixed
	 */
	public function getStatus() {
	
		return 	$this->status;
	
	}
	
	// ======================================================================
	// 									PRIVATE
	// ======================================================================	
	
	private function doRegistration() {
		
		ExtensionManager::registerExtension( get_class( $this ) );
	}
	
	
	/**
	 * Sets hooks
	 * @param $methods array of class methods
	 */
	private function setupHooks( &$methods ){

		global $wgHooks;
		
		$len = strlen( self::$_hook );		
		
		if ( !empty( $methods ) )
			foreach( $methods as $method )
				if ( substr( $method, 0, $len ) == self::$_hook )
					$wgHooks[ substr( $method, $len ) ][] = array( $this, $method );
		
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
	 * Sets the message cache
	 * @return void
	 */
	private function setupMessages() {
	
		global $wgMessageCache;
		
		$msg = $this->getMessages( );
		
			if (!empty( $msg ))
				foreach( $msg as $key => &$value )
					$wgMessageCache->addMessages( $msg[$key], $key );		
	}
	
	/**
	 * Returns the array of i18n messages 
	 */
	public function getMessages( ) {
	
		return self::$msg;
	}
	/** 
	 * Sets the i18n message array
	 * @param $msg array
	 */
	public function setMessages( &$msg ) {
	
		self::$msg = $msg;
	}
	
	// ======================================================================
	// 									PROTECTED
	// ======================================================================	
	
	
	/** 
	 * Returns the name of the extension
	 * i.e. the class name with the optional leading MW_ string
	 */
	protected function getName(){

		$name = get_class( $this );
		return str_replace( 'MW_', '', $name );
	}
	/**
	 * Sets credits details
	 */	
	protected function setCreditDetails( $details, $type = 'other' ) {
		
		global $wgExtensionCredits;
		$wgExtensionCredits[ $type ][] = $details;
	}
	/**
	 * Adds some text to the extension's Description field
	 * in the Credit array (displayed in Special:Version)
	 */
	protected function addToCreditDescription( $message, $name = null ) {
		
		if ( null === $name )
			$name = $this->getName();

		$this->updateCreditField( $message, $name, 'description' );
			
	}

	/**
	 * Adds some text to the extension's Description field
	 * in the Credit array (displayed in Special:Version)
	 */
	protected function updateCreditField( $message, $name, $field, $replace = false ) {
		
		global $wgExtensionCredits;
		
		foreach( $wgExtensionCredits as &$types )
			foreach( $types as $index => &$extension )
				if (isset($extension['name']))		
					if ($extension['name'] == $name )
						if ( true === $replace )
							$extension[ $field ] = $message ;
						else
							$extension[ $field ] .= $message ;									
	}
	
	/**
	 * Registers a class for autoloading
	 * Useful for extensions that use 'stubbing' practice
	 * i.e. only a stub is loaded at first and the 'main'
	 * part is loaded on demand.
	 */
	protected function registerAutoload( $classe, $filename ){
		
		global $wgAutoloadClasses;
		$wgAutoloadClasses[$classe] = $filename;
		
	}
	/**
	 * Sets the status of this extension
	 * Will be retrieved through ExtensionManager
	 * 
	 * @param $status 
	 * @return $this
	 */
	protected function setStatus( $status = self::STATE_OK ) {
		
		$this->status = $status;
		return $this;
		
	}
	
} //end class

//</source>