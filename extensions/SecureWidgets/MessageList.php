<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
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
	
	}

	public function pushMessage( &$msg ) {
	
		$this->liste[] = $msg;
		return $this;
	}
	
} //end class definition