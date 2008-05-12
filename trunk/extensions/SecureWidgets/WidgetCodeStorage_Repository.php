<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class WidgetCodeStorageDatabase
	extends WidgetCodeStorage {

	/**
	 * namespace prefix for the trans-cache
	 */
	static $prefixTransCache = 'sw-';
	
	/**
	 * Constructor
	 */
	public function __construct( ) {
	
		parent::__construct( );
	
	}
	/**
	 * Retrieves the code
	 * 
	 * @return $code string
	 */
	public function getCode() {

		$code = $this->fetchFromTransCache( $this->name );
		if ( $code !== false )
			return $code;
		
		$code = $this->fetchFromRepository( $this->name );
		if ( $code !== false )
			return $code;
			
		$entry = array( 'id' => 'securewidget-csrepo-not-found' );
		$this->pushError( $entry );
		return null;
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
	
		$text = false;
	
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
			return false;
		}
	
		return $text;
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
	
}//[class WidgetCodeStorageDatabase]