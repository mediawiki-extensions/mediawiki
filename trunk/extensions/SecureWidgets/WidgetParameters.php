<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 * 
 * The result list is supported through the base class WidgetIterator
 * 
 */

require_once 'WidgetIterator.php';

class WidgetParameters 
	extends WidgetIterator {

	/**
	 * RESULTS list
	 * @see WidgetIterator
	 */
	#var $liste = array();
	
	/**
	 * 
	 */
	var $rawList = array();

	/**
	 * 
	 */
	var $status = self::NO_STATUS;
	
	/**
	 * 
	 */
	var $inputList = array();
	
	const NO_STATUS       = 0;
	const OK_STATUS       = 1;
	const NO_CODE         = null;
	const NO_PARAMS_FOUND = false;
	
	const DEFAULT_TYPE    = 'string';
	const TYPE_ERROR      = 'invalid';
	
	/**
	 * Constructor
	 */
	public function __construct( &$params ) {
	
		if ( $params !== NO_CODE && $params !== NO_PARAMS_FOUND ) {
		
			$this->rawList = $params;
			$this->status  = self::OK_STATUS;
			
		} else {

			$this->rawList = array();
			$this->status  = $params;
			
		}
	}
	/**
	 * 
	 */
	public function getStatus() {
	
		return $this->status;
	}
	/**
	 * Factory
	 */
	public static function newFromTemplate( &$code ) {
	
		$params = self::extractRawParams( $code );
		$liste  = self::processRawList( $params );
		
		return new WidgetParameters( $liste );
	}
	/**
	 * 
	 */
	private static $paramPattern = '/\{\@\{(.*)\}\@\}/siU';
	
	/**
	 * Extracts the raw parameters of the form:
	 * 		{@{param | type}@}
	 * 
	 */
	protected static function extractRawParams( &$code ) {
	
		if ( empty( $code ))
			return self::NO_CODE;

		$result = preg_match_all( self::$paramPattern, $code, $matches );
		if ( $result === false )
			return NO_PARAMS_FOUND;
			
		return $matches[1];
	}
	/**
	 * 
	 */
	protected static function processRawList( &$params ) {
	
		if ( $params === self::NO_CODE || $params === self::NO_PARAMS_FOUND )
			return $params;

		$plist = array();
			
		foreach( $params as $index =>&$e ) {
		
			$p = array();
			
			$bits = explode( "|", $e );
			
			switch( count( $bits ) ) {
				// param | type
				case 2: 
					$p['p'] = $bits[0];
					$p['t'] = $bits[1];
					break;
									
				// param
				case 1:
					$p['p'] = $bits[0];
					$p['t'] = self::DEFAULT_TYPE;
					break;
					
				// wrong!
				default:
					$p['t'] = self::TYPE_ERROR;
					break;
			} //switch
			
			$plist[] = $p;
			
		}//foreach
		
		return $plist;
	}
	
} //end class definition