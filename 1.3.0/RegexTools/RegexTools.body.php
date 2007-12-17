<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage PermissionFunctions
 * @version 1.3.0
 * @Id $Id: RegexTools.body.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
class RegexTools
{
	// constants.
	const thisName = 'RegexTools';
	const thisType = 'other';
	  
	function __construct( ) {}

	/**
		Returns index in pattern array of *first* pattern match.
		
		@param: patternArrayName:	variable name (found in PageFunctions extension) 
		@param: input:				input string to regex match
	 */
	public function mg_regx_vars( &$parser, &$patternArrayName, &$input )
	{
		// the worst that can happen is that no valid return values are received.
		wfRunHooks('PageVarGet', array( &$patternArrayName, &$parray ) );
		$mIndex = self::regexMatchArray( $parray, $input );	
		
		return $mIndex;
	}
	public function mg_regx( &$parser, &$patternString, &$input )
	{
		return self::regexMatch( $patternString, $input );
	}
	
/*%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%*/
	
	public static function regexMatchArray( &$patternArray, &$input )
	{
		if (!empty( $patternArray ))
			foreach( $patternArray as $index => &$p )
				if ( self::regexMatch( $p, $input ) )
					return $index;
		return null;
	}
	public static function regexMatch( &$p, &$input )
	{
		$pms= '/'.$p.'/siU';

		#echo ' $pms:'.$pms.' $input:'.$input."\n";

		$m = preg_match( $pms, $input );
		if (($m !== false) && ($m>0))
			return true;
			
		return false;
	}
} // end class declaration.
//</source>