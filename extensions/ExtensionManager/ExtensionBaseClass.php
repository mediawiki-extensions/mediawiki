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
	static $_hook = 'hook_';
	static $_ptag = 'ptag_';
	static $_pfnc = 'pfnc_';
	
	/**
	 * i18n messages
	 */
	static $msg = array();
	
	/** 
	 * List of registered parser functions
	 */
	var $pfnc_list = array();
	
	/**
	 * Methods of this class
	 */
	var $methods = array();
	
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
		
		// some initialization must be done
		// prior to the "setup" phase
		$this->init();
			
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
		$this->setupMessages( );
		$this->setupHooks( );
		$this->setupTags(  );
		$this->setupParserFunctions( );
		
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

	/** 
	 * Phase 1 initialization
	 */	
	private function init() {

		global $wgHooks;
		$wgHooks['LanguageGetMagic'][]  = array( $this, "hook_LanguageGetMagic" );

		$this->methods = get_class_methods( $this );
	
		// init the parser functions list
		// for the benefit of the hook 'GetLanguageMagic'
		$this->get_pfnc_list();
	}
	/** 
	 * Retrieves the parser functions list
	 * from the defined methods in the sub-class
	 */
	private function get_pfnc_list() {

		// shortcut
		$methods = $this->methods;
	
		$len = strlen( self::$_pfnc );		
		
		foreach( $methods as $method )
			if ( substr( $method, 0, $len ) == self::$_pfnc ) {
				$key = substr( $method, $len );
				$this->pfnc_list[] = $key;
			}
	}
	
	/**
	 * Registers an extension with ExtensionManager
	 */
	private function doRegistration() {
		
		ExtensionManager::registerExtension( get_class( $this ) );
	}
	
	/**
	 * Sets hooks
	 * @param $methods array of class methods
	 */
	private function setupHooks( ){

		global $wgHooks;
		
		$methods = $this->methods; #shortcut
		
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
	private function setupTags( ){

		global $wgParser;
		
		$methods = $this->methods; #shortcut
				
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
	private function setupParserFunctions( ){

		global $wgParser;
		
		$liste = $this->pfnc_list; #shortcut
		
		if ( empty( $liste ) )
			return;
			
		foreach( $liste as $word )
			$wgParser->setFunctionHook( "$word", array( $this, self::$_pfnc.$word ) );

	}
	/** 
	 * HOOK 'LanguageGetMagic'
	 */
	public function hook_LanguageGetMagic( &$words, $langCode ) {
	
		foreach( $this->pfnc_list as $word )
			$words[ $word ] = array( 0, $word );
			
		return true;
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
	public function setMessages( $msg ) {
	
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

	// ======================================================================
	// 									HELPERS
	// 						FOR PARAMETER LIST PROCESSING
	// ======================================================================	
	
	/**
	 * Handles errors from the ExtensionHelperClass
	 * - Invalid parameters
	 * - Missing mandatory parameters
	 * - Parameters with type error
	 * 
	 * @param $h Object
	 */
	protected function handleErrors( &$h, $baseMsgId ) {
	
		$message = wfMsg( $baseMsgId );
	
		if ( $h->foundMissing() )
			$insertSeparator = $this->handleMissingErrors( $h, $message, $baseMsgId );
			
		if ( $h->foundInvalid() )
			$insertSeparator = $this->handleInvalidErrors( $h, $message, $baseMsgId, $insertSeparator );
			
		if ( $h->foundTypeErrors() )
			$this->handleTypeErrors( $h, $message, $baseMsgId, $insertSeparator );
			
		$message .= '.';

		// include HELP message
		$help_message = wfMsg( $baseMsgId.'-help' );
		$message .= $help_message;		
		
		// include EXAMPLE
		$example_message = wfMsg( $baseMsgId.'-example' );
		$message .= $example_message;		
				
		return array( $message, 'noparse' => true, 'isHTML' => true );	;
	}
	
	/**
	 * Returns a formatted error message
	 * regarding the "missing mandatory parameters"
	 * 
	 * @return $msg string
	 */
	protected function handleMissingErrors( &$h, &$msg, &$baseMsgId ) {
		
		$liste = $h->getMissingList();
		if ( empty( $liste ))
			return false;
		
		foreach( $liste as $index => &$param ) {
		
			if ( $index !== 0 )
				$msg .= ', ';
				
			$msg .= wfMsg( $baseMsgId . '-tpl-missing', $param );
		}
		return true;
	}

	/**
	 * Returns a formatted error message
	 * regarding the "invalid parameters"
	 * 
	 * @return $msg string
	 */
	protected function handleInvalidErrors( &$h, &$msg, &$baseMsgId, $insertSeparator ) {

		$liste = $h->getInvalidList();
		if ( empty( $liste ))
			return false;

		if ( $insertSeparator )
			$msg .= ', ';
			
		foreach( $liste as $index => &$param ) {

			if ( $index !== 0 )
				$msg .= ', ';
				
			$msg .= wfMsg( $baseMsgId . '-tpl-invalid', $param );		
		}
	
		return true;
	}

	/**
	 * Returns a formatted error message
	 * regarding the "type errors in parameters"
	 * 
	 * @return $msg string
	 */
	protected function handleTypeErrors( &$h, &$msg, &$baseMsgId, $insertSeparator ) {
		
		$liste = $h->getTypeErrorsList();
		if ( empty( $liste ))
			return;

		if ( $insertSeparator )
			$msg .= ', ';
			
		foreach( $liste as $index => &$entry ) {

			if ( $index !== 0 )
				$msg .= ', ';
		
			$msg .= wfMsg( $baseMsgId . '-tpl-type', $entry['key'], $entry['type'] );
		}
		
	}
	
	
	
} //end class

//</source>