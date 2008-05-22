<?php
/**
 * @package OnImageUpload
 * @category Enhancements
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {

	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager >= v2.0.1</a>";
	die(-1);
	
}

/**
 * Class definition
 */
class MW_OnImageUpload 
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME    = 'onimageupload';
	
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
	
		parent::__construct();
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
			'description' => 'Provides automated header and footer text to Image description pages on upload',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:OnImageUpload',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	/**
	 * HOOK ArticleSave
	 */
	public function hook_ArticleSave( &$article, &$user, &$text, &$summary, $minor,	$na1, $na2, &$flags) {
	
		// make sure we are dealing with an article in the Image namespace
		
		// make sure it is a new page too
		
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_OnImageUpload;
