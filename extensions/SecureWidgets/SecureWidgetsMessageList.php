<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class MW_SecureWidgetsMessageList 
	implements Iterator {

	var $liste = array();

	public function __construct() {
	}
	
	public function pushMessages( &$liste ) {
	
		if ($liste !instanceof Array)
			throw new Exception( __METHOD__.': list must be an array' );
			
		foreach( $liste as $index => &$msg )
			$this->pushMessage( $msg );
	
	}

	public function pushMessage( &$msg ) {
	
		$this->liste[] = $msg;
		return $this;
	}
	
	/*********************************************************
	 * 				Iterator Interface
	 ********************************************************/	
	public function count() {

		return count( $this->liste );
	}
	public function current() {

		return current( $this->liste );
	}
	public function key() {

		return key( $this->liste );
	}
	public function next() {

		return next( $this->liste );
	}
	public function rewind() {
	
		return rewind( $this->liste );
	}
	public function valid() {

		return valid( $this->liste );
	}
		
	
} //end class definition