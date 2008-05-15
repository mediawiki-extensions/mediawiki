<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {

	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager >= v2.0.1</a>";
	die(-1);
	
}
// These includes will anyhow only get included once
 
/**
 * Class definition
 */
class MW_SecureWidgets 
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets';
	
	private static $components = array(

		'TypeChecker'						=> 'TypeChecker',
	
		'Widget'							=> 'Widget',
		'WidgetParameters'					=> 'WidgetParameters',
		'WidgetIterator'					=> 'WidgetIterator',
		'WidgetLocator'						=> 'WidgetLocator',	
	
		'RepositoryFeed'					=> 'RepositoryFeed',
		'MessageList'						=> 'MessageList',

		'MW_WidgetRenderer'					=> 'WidgetRenderer',	
		'MW_WidgetFactory'					=> 'WidgetFactory',
		'MW_WidgetCodeStorage' 				=> 'WidgetCodeStorage',
		'MW_WidgetCodeStorage_Database' 	=> 'WidgetCodeStorage_Database',
		'MW_WidgetCodeStorage_Repository'	=> 'WidgetCodeStorage_Repository',
		
	);
	
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
	
		global $wgAutoloadClasses;
		
		$dir = dirname( __FILE__ ).'/';
		foreach( self::$components as $classe => &$filename )
			$wgAutoloadClasses[ $classe ] = $dir . $filename . '.php';
	
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
			'description' => 'Provides secure widgets',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:SecureWidgets',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	/**
	 * Parser Function #gliffy
	 */
	public function pfnc_widget( &$parser, $_name = null ) {
	
		if ( empty( $_name )) {
			$msg = new MessageList();
			$msg->pushMessageById( self::NAME . '-missing-name' );
			return $this->handleError( $msg );
		}
	
		$params = func_get_args();	
        array_shift($params); # $parser 
        array_shift($params); # $name
		
        // make sure we are not tricked
        $name = $this->makeSecureName( $_name );
        
        // get Factory istance
        $factory = MW_WidgetFactory::gs();

        // try building a widget from the provided name
        $widget = $factory->newFromWidgetName( $name );
        
		if (!( $widget instanceof Widget ))
			return $this->handleError( $widget );
		
		// render the widget with the provided parameters
		$renderer = MW_WidgetRenderer::gs();
		$output = $renderer->render( $widget, $params );
		
		if ( !is_string( $output ) )
			return $this->handleError( $output );
			
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}
	/**
	 * 
	 */
	protected function handleError( &$obj ) {
	
		if (!( $obj instanceof MessageList ))
			throw new Exception( __METHOD__. ": invalid error object");
			
		$msg = null;
		$msgHeader = wfMsg( self::NAME ) . ': ';
		// use the iterator interface
		foreach( $obj as $msgEntry )
			$msg .= $msgHeader . $msgEntry . "<br/>\n";

		$msg .= $this->getHelpMessage() . "<br/>\n";
		$msg .= $this->getExampleMessage() . "<br/>\n";		
	
		return $msg;
	}
	protected function getExampleMessage() {
	
		return wfMsg( self::NAME . '-example' );	
	}
	/**
	 * 
	 */
	protected function getHelpMessage() {
	
		return wfMsg( self::NAME . '-help' );	
	}
	/**
	 * Validates a Widget name for security reasons
	 * since we will be using this name as key in 
	 * the database downstream
	 * 
	 * @param $_name string
	 * @return $name string
	 */
	protected function makeSecureName( &$name ) {
	
		#$name = strtolower( $_name );
		$name = ltrim( $name, "\'\" \t\n\r\0\x0B" );
		$name = rtrim( $name, "\'\" \t\n\r\0\x0B" );
	
		return $name;
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_SecureWidgets;
include 'SecureWidgets.i18n.php';

include 'WidgetCodeStorage.php';
include 'WidgetCodeStorage_Database.php';
include 'WidgetCodeStorage_Repository.php';

include 'WidgetRenderer.php';