<?php
/**
 * @author Jean-Lou Dupont
 * @package ExtensionManager
 * @category ExtensionManager
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>

class ExtensionHelperClass
{
	/**
	 * Gets filled by the PHING build file
	 */
	const version = '@@package-version@@';
	
	/**
	 * Retrieves the specified list of parameters from the list.
	 * Uses the ''l'' parameter from the reference list.
	 * 
	 * @param $liste array of parameters
	 * @param $ref_liste array of reference parameters
	 * @return $result string of parameters in the form key=value
	 */
	public static function buildList( &$liste, &$ref_liste )	
	{
		if (empty( $liste ))
			return null;
			
		$result = '';
		// only pick the key:value pairs that have been
		// explictly marked using the 'l' key in the
		// reference list.
		foreach( $liste as $key => &$value )
		{
			$key = trim( $key );
			$val = trim( $value );
			if ( isset( $ref_liste[ $key ] ) )
				if ( $ref_liste[ $key ]['l'] === true )
					$result .= " $key='$val'";
		}
		return $result;		
	}
	/**
	 * Sanitize the parameters list. 
	 * Just keeps the parameters defined in the reference list.
	 * 
	 * @param $liste array of parameters
	 * @param $ref_liste array of reference parameters
	 * @return $result array of parameters
	 */
	public static function doListSanitization( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return array();

		// first, let's make sure we only have valid parameters
		$new_liste = array();
		foreach( $liste as $key => &$value )
			if (isset( $ref_liste[ $key ] ))
				$new_liste[ $key ] = $value;
				
		// then make sure we have all mandatory parameters
		foreach( $ref_liste as $key => &$instructions )
			if ( $instructions['m'] === true )
				if ( !isset( $liste[ $key ] ))
					return $key;
					
		// finally, initialize to default values the missing parameters
		foreach( $ref_liste as $key => &$instructions )
			if ( $instructions['d'] !== null )
				if ( !isset( $new_liste[ $key ] ))
					$new_liste[ $key ] = $instructions['d'];
				
		return $new_liste;
	}
	/**
	 * Performs various sanitization.
	 * Only valid parameters should end-up here.
	 * 
	 * @param $liste array of parameters
	 * @param $ref_liste array of reference parameters
	 */
	public static function doSanitization( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return null;
			
		foreach( $liste as $key => &$value )
		{
			// Remove leading & trailing double-quotes
			if (isset( $ref_liste[ $key ]['dq'] ))
					if ( $ref_liste[ $key ]['dq'] === true )
					{
						$value = ltrim( $value, "\" \t\n\r\0\x0B" );
						$value = rtrim( $value, "\" \t\n\r\0\x0B" );
					}

			// Remove leading & trailing single-quotes
			if (isset( $ref_liste[ $key ]['sq'] ))
					if ( $ref_liste[ $key ]['sq'] === true )
					{
						$value = ltrim( $value, "\' \t\n\r\0\x0B" );
						$value = rtrim( $value, "\' \t\n\r\0\x0B" );
					}
						

			// HTML sanitization
			if (isset( $ref_liste[ $key ]['s'] ))
				if ( $ref_liste[ $key ]['s'] === true )
					$value = htmlspecialchars( $value );
		}
	}
	/**
	 * Checks for if the $liste contains parameters marked as ''r'' (i.e. restricted)
	 *
	 * @return bool null for empty list
	 * @return string restricted key name
	 * @return bool false if no restricted parameter found
	 */
	public static function checkListForRestrictions( &$liste, &$ref_liste )
	{
		if (empty( $liste ))
			return null;

		foreach( $liste as $key => &$value )
		{
			// HTML sanitization
			if (isset( $ref_liste[ $key ]['r'] ))
				if ( $ref_liste[ $key ]['r'] === true )
					return $key;							
			
		}
		
		return false;		
	}
}// end class definition

