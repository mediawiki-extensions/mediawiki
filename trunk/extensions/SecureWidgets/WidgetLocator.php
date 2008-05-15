<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class WidgetLocator {

	var $obj = null;

	const ANCHOR_PATTERN    = '/\<a (.*)\>/siU';
	const CLASS_PATTERN     = '/class\=[\'\"](.+)[\'\"]/siU';
	const HREF_PATTERN 		= '/href=[\'\"](.+)[\'\"]/siU';

	var $params = array();
	
	static $paramsList = array(
	
		'codelink' => true,
		'helplink' => true,
		'version'  => true,
	);
	
	public function __construct( &$obj ) {
	
		$this->obj = $obj;
	
	}

	public function __get( $key ) {
	
		if ( !array_key_exists( $key, self::$paramsList ))
			throw new Exception( __METHOD__ .": invalid parameter ($key)");
		
		$this->extractParams();
	
		return $this->params[ $key ];
	}
	
	protected function extractParams() {
	
		// do this only once!
		if ( !empty( $this->params ) )
			return;
		
		foreach( self::$paramsList as $key => $extra ) {
		
			$method = "extract_$key";
			$this->$method();
		}
			
	}
	protected function extract_codelink() {
	
	}
	protected function extract_helplink() {
	
	}
	protected function extract_version() {
	
	}
	
}//end class