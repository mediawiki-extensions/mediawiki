<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

abstract class WidgetCodeStorage 
	extends ExtensionBaseClass {

	/**
	 * Widget Name
	 */
	var $name = null;

	/**
	 * Error list
	 */
	var $errMsgList = array();
	
	/**
	 * Constructor
	 */
	public function __construct( &$name ) {
	
		$this->name = $name;
	}
	/**
	 * Retrieve the code from the storage
	 */
	abstract public function get();

	/**
	 * Returns the HTML version of the
	 * last error message
	 */
	public function getLastErrorMessages() {
	
		if ( empty( $this->errMsgList ) )
			return null;
		
		$msg = '';
		foreach( $this->errMsgList as $entry ) {
		
			$id = $entry['id'];
			$p  = @$entry['p'];

			$f  = 'return wfMsg( $id ';
			
			// create parameter list as coma delimited string
			if ( !empty( $p )) {
				foreach( $p as $e )
					$f .= ", $e";
				$f .= ');';
			}
			else
				$f .= ');';
			
			$msg .= eval( $f );
		}
			
		return $msg;
	}
	
	protected function pushError( &$entry ) {
		$this->errMsgList[] = $entry;
		return $this;
	}
	/**
	 * Returns the error status
	 */
	public function isError() {
		return ( !empty( $this->errMsgIdList ) );
	}
}