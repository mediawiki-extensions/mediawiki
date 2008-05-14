<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

abstract class MW_WidgetCodeStorage 
	extends ExtensionBaseClass {

	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets';
		
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
	public function __construct( ) {
	
		parent::__construct();
	}
	
	public function setName( &$name ) {
	
		$this->name = $name;	
	}
	/**
	 * Retrieve the code from the storage
	 */
	abstract public function getCode();

	/**
	 * Returns the HTML version of the
	 * last error message
	 * 
	 * @return $msg Array of strings
	 */
	public function getLastErrorMessages() {
	
		if ( empty( $this->errMsgList ) )
			return null;
		
		$msg = array();
		foreach( $this->errMsgList as $entry ) {
		
			$id = $entry['id'];
			$p  = @$entry['p'];

			$f  = 'return wfMsg( $id ';
			
			// create parameter list as coma delimited string
			if ( !empty( $p )) {
				foreach( $p as $e )
					if ( is_string( $e ) ) 
						$f .= ", '$e'";
					else
						$f .= ", $e";
				$f .= ');';
			}
			else
				$f .= ');';
			
			#echo __METHOD__." id=$id  f=$f \n";
			$msg[] = eval( $f );
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
		return ( !empty( $this->errMsgList ) );
	}
}