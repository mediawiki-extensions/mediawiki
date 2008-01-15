<?php
/**
 * @author Jean-Lou Dupont
 * @package Ohloh
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class Ohloh
{
	const thisType = 'other';
	const thisName = 'Ohloh';

	// For Messages
	static $msg = array();

	// Error Codes
	const codeMissingParameter  = 1;
	const codeInvalidRef  		= 2;
	const codeListEmpty         = 3;

	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 */
	static $parameters = array(
		// Parameters:
		'account'	=> array( 'm' => true,  's' => true, 'l' => false, 'd' => null,   'sq' => true, 'dq' => true  ),
		'ref'		=> array( 'm' => false, 's' => true, 'l' => false, 'd' => 'Tiny', 'sq' => true, 'dq' => true  ),
		'alt'		=> array( 'm' => false, 's' => true, 'l' => true,  'd' => null,   'sq' => true, 'dq' => true  ),
		'width'		=> array( 'm' => false, 's' => true, 'l' => true,  'd' => '80',   'sq' => true, 'dq' => true  ),
		'height'	=> array( 'm' => false, 's' => true, 'l' => true,  'd' => '15',   'sq' => true, 'dq' => true  ),		
	);
	static $map = array(
		'tiny'		=> array( 'ref' => 'Tiny',     'src' => 'http://www.ohloh.net/images/icons/ohloh_profile.png' ),
		'rank'		=> array( 'ref' => 'Rank',     'src' => 'http://www.ohloh.net/accounts/$1/widgets/account_rank.gif' ),
		'detailed'	=> array( 'ref' => 'Detailed', 'src' => 'http://www.ohloh.net/accounts/$1/widgets/account_detailed.gif' ),
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
	 * {{#mg_ohloh: account=account-number [|optional parameters] }}
	 */
	public function mg_ohloh( &$parser )
	{
		$params = func_get_args();
		$liste = StubManager::processArgList( $params, true );		
		
		$output = $this->renderEntry( $liste );

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	/**
	 * Returns 1 fully rendered HTML element
	 */
	protected function renderEntry( &$liste )
	{
		// check mandatory parameters
		$sliste= ExtHelper::doListSanitization( $liste, self::$parameters );
		if (!is_array( $sliste ) || empty( $sliste ))
			return $this->getErrorMsg( self::codeMissingParameter, $sliste);

		$attrListe = null;
		$r = ExtHelper::doSanitization( $sliste, self::$parameters );
		$attrListe = ExtHelper::buildList( $sliste, self::$parameters );
		
		$account = $sliste['account'];
		
		$ref = $sliste['ref'];
		$realRef = $this->getRef( $ref );
		if ( $realRef === false )
			return $this->getErrorMsg( self::codeInvalidRef, $ref );		
		
		$src = $this->getSrc( $account, $realRef );
		
		$output = <<<EOF
		<a href="http://www.ohloh.net/accounts/$account/?ref=$realRef">
			<img $src $attrListe/>
		</a>
EOF;

		return $output;
	}
	/**
	 * Returns a fully formatted 'src' attribute
	 */
	protected function getSrc( &$account, $ref )	 
	{
		$ref = strtolower( $ref );
		$src = self::$map[ $ref ]['src'];
		
		$a = str_replace('$1', $account, $src );
		return "src='$a'";
	}
	/**
	 * Returns a valid 'ref' attribute or false otherwise
	 */	
	protected function getRef( $ref )
	{
		$ref = strtolower( $ref );

		if (!isset( self::$map[ $ref ] ))
			return false;
			
		return self::$map[ $ref ]['ref'];
	}
	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 * Returns the corresponding error message
	 */
	protected function getErrorMsg( $code, $param = null )
	{
		return wfMsgForContent( 'ohloh'.$code, $param );	
	}

} // end class
require 'Ohloh.i18n.php';
//</source>
