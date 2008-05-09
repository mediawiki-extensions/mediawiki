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

include "WidgetCodeStorage.php";
include "WidgetCodeStorage_Database.php";
include "WidgetCodeStorage_Repository.php";

/**
 * Class definition
 */
class MW_SecureWidgets 
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets';
	
	var $codeStores = array();
	
	/** 
	 * Widget repository
	 */
	static $repositoryURL = "http://mediawiki.googlecode.com/svn/widgets/";
	
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
		
		parent::__construct();

		$this->registerStorage();
	}
	/**
	 * 
	 */
	public function registerStorage( ) {
	
		$this->codeStore[] = new MW_WidgetCodeStorage_Database;
		$this->codeStore[] = new MW_WidgetCodeStorage_Repository;		
		return $this;
	
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
	public function pfnc_widget( &$parser, $_name ) {
	
		$params = func_get_args();	
        array_shift($params); # $parser 
        array_shift($params); # $name
		
        // make sure we are not tricked
        $name = $this->makeSecureName( $_name );
        
        // make sure we have some code to work with
        $code = $this->fetchWidgetCode( $name );
        if ( $code === false )
        	return $this->processNoCodeError( $name );
        
        $inputParameters = $this->extractRequiredInputParameters( $code );
        
        	
		$_p = $this->processParameters( $params );
		if ( $this->isError( $_p ) )
			return $this->processErrors( $_p );	
		
		
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}
	
	/**
	 * Validates a Widget name for security reasons
	 * since we will be using this name as key in 
	 * the database downstream
	 * 
	 * @param $_name string
	 * @return $name string
	 */
	protected function makeSecureName( &$_name ) {
	
		$name = strtolower( $_name );
		$name = ltrim( $name, "\'\" \t\n\r\0\x0B" );
		$name = rtrim( $name, "\'\" \t\n\r\0\x0B" );
	
		return $name;
	}
	
	
	/**
	 * Fetches a widget's code
	 * 
	 * @param $name string
	 * @return $code mixed
	 */
	protected function fetchWidgetCode( &$name ) {
	
	
	}
	
	
	protected function getWidgetVersion( &$code ) {
	
	}
	
	
	
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_SecureWidgets;

include 'SecureWidgets.i18n.php';
