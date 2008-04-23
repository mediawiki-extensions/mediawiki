<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 * @category ExtensionManager
 * @version @@package-version@@
 */
//<source lang=php>

require_once 'ExtensionBaseClass.php';
require_once 'ExtensionHelperClass.php';
require_once 'ExtensionLoader.php';

class ExtensionManager extends ExtensionBaseClass 
{
	/**
	 * Array of registered extensions
	 * @private
	 */
	static $_registeredExtensionsList = array();
	
	public function __construct() {
		parent::__construct();
	}
	/**
	 * Setup
	 */
	protected function setup() {
		
		$this->setCreditDetails( array( 
			'name'        => $this->getName(), 
			'version'     => '@@package-version@@',
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Provides management of MediaWiki extensions. ',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:ExtensionManager',			
			) );

	}
	/**
	 * Example hook (Special:Version page)
	 */	
	public function hookSpecialVersionExtensionTypes( &$sp, &$extensionTypes ) {

		$this->addToCreditDescription( 
			" Using real cache: " . ExtensionLoader::realCacheStatus() . '. '
		);
		
		// Is the PEAR::Validate package available?
		if ( !class_exists( 'Validate') )
			$this->addToCreditDescription( 
				" PEAR::Validate package not available. "
			);
		
		
		// Per-Extension 'decorator'
		foreach( self::$_registeredExtensionsList as &$classe ) {
			
			wfRunHooks( 'ExtensionManager_Credits', 
				array( $classe, &$name, &$replaceName, &$desc, &$replaceDesc ) );
			
			$this->updateCreditField( $name, $classe, 'name', $replaceName );
			$this->updateCreditField( $desc, $classe, 'description', $replaceDesc );
						
		}
		
		// required for all hooks
		return true; #continue hook-chain
	}
	
	/**
	 * Registers an extension with this manager
	 */
	public static function registerExtension( $classe ) {
		
		self::$_registeredExtensionsList[] = $classe;
	}
	
	
	// ======================================================================
	// 									HOOKS
	// ======================================================================	
	
	/**
	 * HOOK 'ExtensionManagerGetList'
	 * 
	 * @param $liste reference to array of registered extensions i.e. class name
	 * @return $result boolean Standard MediaWiki return code
	 */
	public function hookExtensionManagerGetList( &$liste ) {
	
		$liste = self::$_registeredExtensionsList;
		
		return true;
	}
	
	
} //end class

new ExtensionManager;

//</source>