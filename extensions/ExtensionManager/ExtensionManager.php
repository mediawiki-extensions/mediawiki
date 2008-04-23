<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 * @category ExtensionManager
 * @version @@package-version@@
 */
//<source lang=php>

require_once 'ExtensionLoader.php';
require_once 'ExtensionBaseClass.php';
require_once 'ExtensionHelperClass.php';

class ExtensionManager extends ExtensionBaseClass 
{
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
			" Using real cache: " . ExtensionLoader::realCacheStatus() . '.'
		);
				
		// required for all hooks
		return true; #continue hook-chain
	}
	
	
} //end class

new ExtensionManager;

//</source>