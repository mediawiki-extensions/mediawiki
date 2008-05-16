<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version 1.0.0 
 * @Id $Id$
 */

require_once 'WidgetIterator.php';

class MessageList
	extends WidgetIterator { 

	public function __construct() {
	
	}
	public function pushMessages( &$liste ) {
	
		if (!is_array( $liste ))
			throw new Exception( __METHOD__.': list must be an array' );
			
		foreach( $liste as $index => &$msg )
			$this->pushMessage( $msg );
	
		return $this;
	}
	public function isEmpty() {
	
		return empty( $this->liste );
	}
	
	public function pushMessage( &$msg ) {
	
		$this->liste[] = $msg;
		return $this;
	}
	
	public function pushMessageById( $id, $p = null ) {
	
		$f  = 'return wfMsg( $id ';
		
		// create parameter list as coma delimited string
		if ( !empty( $p )) {

			foreach( $p as $e ) {
				if ( is_string( $e ) ) 
					$f .= ", '$e'";
				else
					$f .= ", $e";
			}
		}
		
		$f .= ' );';
		
		$this->liste[] = eval( $f );
		
		// chainability
		return $this;
	}
	public function insertMessages( &$msgObj ) {
		foreach( $msgObj as $msg )
			$this->liste[] = $msg;
	}
} //end class definition