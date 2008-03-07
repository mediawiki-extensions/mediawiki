<?php
/**
 * @author Jean-Lou Dupont
 * @package Quimble
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class Quimble
{
	const thisType = 'other';
	const thisName = 'Quimble';

	// For Messages
	static $msg = array();

	// Error Codes
	const codeMissingParameter  = 1;
	const codeListEmpty         = 2;

	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 * r: restricted
	 * sq: single-quote escape
	 * dq: double-quote escape
	 */
	static $parameters = array(
		// Parameters:
		'index'	=> array( 'm' => true,  's' => true, 'l' => false, 'd' => '9257',   'sq' => true, 'dq' => true  ),
	);
	/**
	 * Initialize the messages
	 */
	public function __construct()
	{
		global $wgMessageCache;

		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}	 
	/**
	 * {{#quimble_poll: index=poll_index }}
	 */
	public function mg_quimble_poll( &$parser )
	{
		$params = func_get_args();
		$liste = StubManager::processArgList( $params, true );		
		
		$code = $this->formatPoll( $liste, $index, $output );

		if ( $code !== true )
			return $code;
			
		return array( $output, 'noparse' => true, 'isHTML' => true );			
	}
	/**
	 *
	 */	
	protected function formatPoll( &$liste, &$index, &$output )
	{
		// check mandatory parameters
		$sliste= ExtHelper::doListSanitization( $liste, self::$parameters );
		if (empty( $sliste ))
			return $this->getErrorMsg( self::codeListEmpty );
		
		if (!is_array( $sliste ))
			return $this->getErrorMsg( self::codeMissingParameter, $sliste);

		$attrListe = null;
		$r = ExtHelper::doSanitization( $sliste, self::$parameters );
		$attrListe = ExtHelper::buildList( $sliste, self::$parameters );
		
		$index = $sliste['index'];

		$output = "<script type='text/javascript' src='http://quimble.com/inpage/index/${index}'></script>";
		
		return true;
	}	 
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * Returns the corresponding error message
	 */
	protected function getErrorMsg( $code, $param = null )
	{
		return wfMsgForContent( 'quimble'.$code, $param );	
	}

} // end class
require 'Quimble.i18n.php';
//</source>
