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

/*
 *   { 'n'  =>  param-name,
 *     't'  =>  param-type,
 *	   'v'  =>  param-value }
 * 
 */

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
	const PARAM_ERROR     = 'param-error';
	
	/**
	 * Constructor
	 */
	public function __construct( &$params ) {
	
		if ( $params !== NO_CODE && $params !== NO_PARAMS_FOUND ) {
		
			$this->liste = $params;
			$this->status  = self::OK_STATUS;
			
		} else {

			$this->liste = array();
			$this->status  = $params;
			
		}
	} //__construct
	
	/**
	 * Verifies if a given param exists
	 * @return mixed $index if found, FALSE otherwise
	 */
	public function isParam( &$name ) {

		// assume worst case
		$result = false;
		
		foreach( $this->liste as $index => &$e ) {
		
			if ( isset( $e['n'] ) )
				if ( $e['n'] == $name )
					$result = $index;
		}
		
		return $result;
	}
	
	public function getParamByIndex( &$index ) {
	
		if ( isset( $this->liste[ $index ] ) )
			return $this->liste[ $index ];
		
		throw new Exception( __METHOD__. ": invalid index" );
	}
	/**
	 * 
	 */
	public function getStatus() {
	
		return $this->status;
	}
	
	public function isEmpty() {
		return ( $this->status === self::NO_PARAMS_FOUND );
	}
	/******************************************************************
	 * 						TEMPLATE related
	 ******************************************************************/
	
	/**
	 * Factory
	 */
	public static function newFromTemplate( &$code ) {
	
		$params = self::extractRawParamsFromTemplate( $code );
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
	protected static function extractRawParamsFromTemplate( &$code ) {
	
		if ( empty( $code ))
			return self::NO_CODE;

		$result = preg_match_all( self::$paramPattern, $code, $matches );
		if ( $result === false )
			return NO_PARAMS_FOUND;
			
		return $matches[1];
	}
	/**
	 * Each array element:
	 * 		{ 'n' => param-name, 't' => required-type }
	 */
	protected static function processRawList( &$params, $delimiter ='|' ) {
	
		if ( $params === self::NO_CODE || $params === self::NO_PARAMS_FOUND )
			return $params;

		$plist = array();
			
		foreach( $params as $index =>&$e ) {
		
			$p = array();
			
			$bits = explode( $delimiter, $e );
			
			switch( count( $bits ) ) {
				// param-name | type
				case 2: 
					$p['n'] = $bits[0];
					$p['t'] = $bits[1];
					break;
									
				// param
				case 1:
					$p['n'] = $bits[0];
					$p['t'] = self::DEFAULT_TYPE;
					break;
					
				// wrong!
				default:
					$p['t'] = self::PARAM_ERROR;
					break;
			} //switch
			
			$plist[] = $p;
			
		}//foreach
		
		return $plist;
	}

	/******************************************************************
	 * 						Parameter List related
	 * 						e.g.  param-name=param-value
	 ******************************************************************/
	
	public static function newFromParamList( &$params ) {
	
		$pl = self::processRawParamsList( $params, '=' );
		
		return new WidgetParameters( $pl );
	}
	/**
	 * Process list of the form 'key=value'
	 */
	protected function processRawParamsList( &$params, $delimiter = '=' ) {
	 
		if ( empty( $params ) )
			return self::NO_PARAMS_FOUND;
			
		$pl = array();
		
		foreach( $params as $index => &$e ) {
			
			$p = array();
			$bits = explode( $delimiter, $e );
			
			switch( count( $bits ) ) {
			
				// normal case
				case 2:
					$p['n'] = $bits[0];
					$p['v'] = $bits[1];
					break;
				
				default:
					$p['t'] = self::PARAM_ERROR;
					break;
			}//switch
			
			$pl[] = $p;
			
		}//foreach
		
		return $pl;
	}//method
	
} //end class definition