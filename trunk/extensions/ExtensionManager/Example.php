<?php

// No need to include the dependency
// as it is already included by default
// through ExtensionManager
#require_once "ExtensionBaseClass.php";

class MW_ExampleExtension extends ExtensionBaseClass
{
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
		parent::__construct();
		
		// do some stuff starting here
	}

	/**
	 * Optional setup 
	 * This method can be omitted
	 */
	protected function setup(){
		global $wgExtensionCredits;

		$name = get_class( $this );
		$name = str_replace( 'MW_', '', $name );
				
		$wgExtensionCredits['other'][] = array( 
			'name'        => $name, 
			'version'     => '@@package-version@@',
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Some description. ',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:ExtensionManager',			
		);
	
	}
	/**
	 * Example hook (Special:Version page)
	 */	
	public function onSpecialVersionExtensionTypes( &$sp, &$extensionTypes ){
		global $wgExtensionCredits;
		
		$name = get_class( $this );
		$name = str_replace( 'MW_', '', $name );
		
		foreach( $wgExtensionCredits as &$types )
			foreach( $types as $index => &$extension )
				if (isset($extension['name']))		
					if ($extension['name'] == $name )
						$extension['description'] .= "Some Status".'<br/>';			
		
		// required
		return true;
	}
	
}//end class definition

new MW_ExampleExtension;