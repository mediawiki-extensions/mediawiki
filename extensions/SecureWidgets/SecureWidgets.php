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

/**
 * Class definition
 */
class MW_SecureWidgets 
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets';
	
	/**
	 * namespace prefix for the trans-cache
	 */
	static $prefixTransCache = 'sw-';
	
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
			'description' => 'Provides ',
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
	 */
	protected function fetchWidgetCode( &$name ) {
	
		// try the trans-cache
		$code = $this->fetchFromTransCache( $name );
		if ( $code !== false ) return $code;
		
		// try the page-cache
		$code = $this->fetchFromPageCache( $name );
		if ( $code !== false ) return $code;

		// try the in the "Widget" namespace		
		$code = $this->fetchFromPage( $name );
		if ( $code !== false ) return $code;
		
		// try the external repository
		$code = $this->fetchFromRepository( $name );
		if ( $code !== false ) return $code;
			
		return false;	
	
	}
	
	/**
	 * Fetches a widget's code from the external Repository
	 * 
	 * @param $name string Name of the widget
	 * @return $code mixed
	 */
	protected function fetchFromRepository( &$name ) {
	
		$url = $this->formatNameForRepository( $name );
		
		$code = Http::get( $url );
		if ( $code === false )
			return false;
	
		// if we got lucky, save it to the trans-cache
		$this->saveInTransCache( $name, $code );
	
		return $code;
	}
	
	/**
	 * Formats an URL for accessing the external repository
	 * 
	 * @param $name string
	 * @return $url string
	 */
	protected function formatNameForRepository( &$name ) {
	
		return self::$repositoryURL . $name . '/' . $name . '.wikitext' ;
		
	}
	
	/**
	 * Fetches a widget's code from the trans-cache
	 * 
	 * @param $name string Name of the widget
	 * @return $code mixed
	 */
	protected function fetchFromTransCache( &$name ) {
	
		$url = $this->formatNameForTransCache( $name );
	
		global $wgTranscludeCacheExpiry;
		$dbr = wfGetDB(DB_SLAVE);
		$obj = $dbr->selectRow(	'transcache', 
								array('tc_time', 'tc_contents'),
								array('tc_url' => $url ));
		if ($obj) {
			$time = $obj->tc_time;
			$text = $obj->tc_contents;
			if ($time && time() < $time + $wgTranscludeCacheExpiry ) {
				return $text;
			}
		}
	
		return $text;
	}
	/** 
	 * Fetches a widget's code from the page database
	 */
	protected function fetchFromPage( &$name ) {
	
	}

	/**
	 * Fetches a widget's code from the parser cache
	 */
	protected function fetchFromPageCache( &$name ) {
	
	}
	

	/**
	 * Saves a widget's code in the trans-cache
	 * @param $name string
	 * @param $code string
	 * @return $result boolean
	 */	
	protected function saveInTransCache( &$name, &$code ) {
	
		$url = $this->formatNameForTransCache( $name );
	
		$dbw = wfGetDB(DB_MASTER);
		$dbw->replace(	'transcache', 
						array(	'tc_url' ), 
						array(	'tc_url' 	=> $url,
								'tc_time' 	=> time(),
								'tc_contents' => $code ));
		return true;
	
	}
	
	/**
	 * Generates a valid key for the trans-cache
	 * 
	 * @param $name string
	 * @return $key string
	 */
	protected function formatNameForTransCache( &$name ) {
	
		return self::$prefixTransCache . $name ;
	
	}
	
	protected function getWidgetVersion( &$code ) {
	
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_SecureWidgets;

include 'SecureWidgets.i18n.php';
