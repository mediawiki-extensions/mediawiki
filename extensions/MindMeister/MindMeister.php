<?php
/**
 * @package ExtensionManager
 * @category ExtensionManager
 * @author Jean-Lou Dupotn
 * @version @@package-version@@ 
 * @Id $Id$
 */

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
		'mm_width'		=> array( 'm' => true, 'u' => true, 'n' => 'width' , 's' => false, 'l' => false, 'd' => null, 't' => 'number' ),
		'mm_height'		=> array( 'm' => true, 'u' => true, 'n' => 'height', 's' => false, 'l' => false, 'd' => null, 't' => 'number' ),
		'mm_zoom'		=> array( 'm' => false, 'u' => true, 'n' => 'zoom',   's' => false, 'l' => false, 'd' => null, 't' => 'number' ),	

		#HTML (iframe) specified in example from mindmeister	
		'width'		=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 'sq' => true, 'dq' => true ),
		'height'	=> array( 'm' => false,  's' => false, 'l' => true, 'd' => null, 'sq' => true, 'dq' => true ),
	
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
			return $this->handleErrors( $h, 'mindmeister' );
			
		$output = $this->format( $h );
			
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}
	/**
	 * Formats the required HTML
	 */
	protected function format( &$h ) {
	
		$liste = $h->getOutputList();
		
		$url_params_liste = array();
		
		// mandatory
		$mmid   = $liste[ 'mmid' ];
		
		$url_params =  $h->getUrlString();
				
		$url_params = $mmid . $url_params;
		
		$html_params = $h->getStringList( );
		
		$html = wfMsg( 'mindmeister-html', $url_params, $html_params );
		
		return $html;
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_MindMeister;

// i18n Messages
include 'MindMeister.i18n.php';
