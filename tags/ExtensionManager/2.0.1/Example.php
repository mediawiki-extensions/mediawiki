<?php
/**
 * @package ExtensionManager
 * @category ExtensionManager
 * @author Jean-Lou Dupotn
 * @version 2.0.1 
 * @Id $Id: Example.php 1043 2008-04-24 16:58:57Z jeanlou.dupont $
 */
// No need to include the dependency
// as it is already included by default
// through ExtensionManager.
// NOTE that 'require_once' is a slow  process
#require_once "ExtensionBaseClass.php";

class MW_ExampleExtension 
	extends ExtensionBaseClass
{
	const VERSION = '2.0.1';
	
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
		
		parent::__construct();
		
		// do some stuff starting here
		// NOTE: don't touch too much MediaWiki's internals here;
		//       use the 'setup' method to do this instead.
	}

	/**
	 * Optional setup: called once it is safe
	 *  to perform additional setup on the MediaWiki platform.
	 * 
	 * @optional This method can be omitted.
	 */
	protected function setup(){
		
		$this->setCreditDetails( array( 
			'name'        => $this->getName(), 
			'version'     => self::VERSION,
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Some description. ',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:ExtensionManager',			
			) );
		
		$this->setStatus( self::STATE_OK );
			
		// do some other stuff here
	}
	/**
	 * Example hook (Special:Version page)
	 */	
	public function hook_SpecialVersionExtensionTypes( &$sp, &$extensionTypes ){

		$this->addToCreditDescription( "Some Status Message<br/>" );
				
		// required for all hooks
		return true; #continue hook-chain
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_ExampleExtension;