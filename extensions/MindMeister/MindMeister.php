<?php
/**
 * @package ExtensionManager
 * @category ExtensionManager
 * @author Jean-Lou Dupotn
 * @version @@package-version@@ 
 * @Id $Id$
 */
// No need to include the dependency
// as it is already included by default
// through ExtensionManager.
// NOTE that 'require_once' is a slow  process
#require_once "ExtensionBaseClass.php";

if (!class_exists( 'ExtensionBaseClass' )) {
	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager</a>";
	die(-1);
}

class MW_MindMeister 
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	
	static $parameters = array(
	
		#URL parameters
		'mmid'			=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null, 't' => 'number' ),
		'width'			=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null, 't' => 'number' ),
		'height'		=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null, 't' => 'number' ),
		'zoom'			=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null, 't' => 'number' ),	

		#HTML (iframe) specified in example from mindmeister	
		'frame_width'	=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 't' => 'number', 'sq' => true, 'dq' => true ),
		'frame_height'	=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 't' => 'number', 'sq' => true, 'dq' => true ),
	
		#HTML specified in example from mindmeister
		'style'			=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 'sq' => true, 'dq' => true ),
		'frameborder'	=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 'sq' => true, 'dq' => true ),	
		'scrolling'		=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 't' => 'string', 'sq' => true, 'dq' => true ),		
		
		#HTML
		'id'			=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 'sq' => true, 'dq' => true ),	
		'class'			=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 'sq' => true, 'dq' => true ),	
	
	);
	/**
	 * Embedding URL
	 */
	static $url = 'http://www.mindmeister.com/maps/public_map_shell/$id?width=$width&height=$height&zoom=$zoom';
	
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
			'description' => 'Provides integration with [http://www.mindmeister.com MindMeister] mindmaps.',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:MindMeister',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	
	public function pfuncMindMeister( &$parser ) {
	
		$params = func_get_args();	
		
		$p = ExtensionHelperClass::processArgList( $params );
		
		$h = new ExtensionHelperClass( $p, self::$parameters );
	
		if ( $h->isError() )
			return $this->handleErrors( $h );
	}
	
	/**
	 * Handles errors from the ExtensionHelperClass
	 * - Invalid parameters
	 * - Missing mandatory parameters
	 * - Parameters with type error
	 * 
	 * @param $h Object
	 */
	protected function handleErrors( &$h ) {
	
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_MindMeister;