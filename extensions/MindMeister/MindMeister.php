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
	
	public function pfnc_mindmeister( &$parser ) {
	
		$params = func_get_args();	
		
		$p = ExtensionHelperClass::processArgList( $params, true );
		
		$h = new ExtensionHelperClass( $p, self::$parameters );
	
		if ( $h->isError() )
			return $this->handleErrors( $h );
			
		$output = $this->format( $h );
			
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}
	/**
	 * Formats the required HTML
	 */
	protected function format( &$h ) {
	
		$liste = $h->getOutputList();
		
		$mmid   = $liste[ 'mmid' ];
		$width  = $liste[ 'width' ];
		$height = $liste[ 'height' ];
		$zoom   = $liste[ 'zoom' ];
		
		$params = $h->getStringList( );
		
		$html = wfMsg( 'mindmeister-html', $mmid, $width, $height, $zoom, $params );
		
		return $html;
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
	
		$message = wfMsg( 'mindmeister' );
	
		if ( $h->foundMissing() )
			$this->handleMissingErrors( $h, $message );
			
		if ( $h->foundInvalid() )
			$this->handleInvalidErrors( $h, $message );
			
		if ( $h->foundTypeErrors() )
			$this->handleTypeErrors( $h, $message );
			
		return $message;
	}
	
	/**
	 * Returns a formatted error message
	 * regarding the "missing mandatory parameters"
	 * 
	 * @return $msg string
	 */
	protected function handleMissingErrors( &$h, &$msg ) {
		
		$liste = $h->getMissingList();
		
		foreach( $liste as $param )
			$msg .= wfMsg( 'mindmeister-tpl-missing', $param );
	}

	/**
	 * Returns a formatted error message
	 * regarding the "invalid parameters"
	 * 
	 * @return $msg string
	 */
	protected function handleInvalidErrors( &$h, &$msg ) {

		$liste = $h->getInvalidList();
		
		foreach( $liste as $param )
			$msg .= wfMsg( 'mindmeister-tpl-invalid', $param );
	
	}

	/**
	 * Returns a formatted error message
	 * regarding the "type errors in parameters"
	 * 
	 * @return $msg string
	 */
	protected function handleTypeErrors( &$h, &$msg ) {
		
		$liste = $h->getTypeErrorsList();
		
		foreach( $liste as $param => &$type )
			$msg .= wfMsg( 'mindmeister-tpl-type', $param, $type );
	
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_MindMeister;

// i18n Messages
include 'MindMeister.i18n.php';
