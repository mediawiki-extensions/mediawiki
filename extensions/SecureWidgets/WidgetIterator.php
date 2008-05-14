<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class WidgetIterator
	implements Iterator {
	
	var $liste = array();
	
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
	
		return reset( $this->liste );
	}
	public function valid() {

		return ( key( $this->liste ) !== null );
	}
	
} // end class definition