<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ForeachFunction
 * @version 1.3.0
 * @Id $Id: ForeachFunction.body.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
class ForeachFunction
{
	// constants.
	const thisName = 'ForeachFunction';
	const thisType = 'other';

	const typeUnknown = 0;
	const typeGLOBAL  = 1;
	const typeArray   = 2;
	const typeObject  = 3;
	const typeClass   = 4;
	const typeString  = 5;

	// Namespace exemption functionality
	static $enableExemptNamespaces = true;
	static $exemptNamespaces = array();
	
	public static function addExemptNamespaces( $list )
	{
		if (!is_array( $list ))	
			$list = array( $list );
			
		self::$exemptNamespaces = array_merge( self::$exemptNamespaces, $list );
	}
	
	function __construct( )
	{
		// default exempt namespaces from the BizzWiki platform.
		// won't affect installs of the extension outside the BizzWiki platform.
		if (defined('NS_BIZZWIKI'))   self::$exemptNamespaces[] = NS_BIZZWIKI;
		if (defined('NS_FILESYSTEM')) self::$exemptNamespaces[] = NS_FILESYSTEM;
	}
	public function mg_foreachx( &$parser, &$object, &$property, &$pattern, &$param1 = null, &$param2 = null )
	// {{#foreachx:global variable name||pattern}}	
	// {{#foreachx:global object name|property name|pattern}}
	// {{#foreachx:global object name|method name  |pattern}}	
	// Works on 'array' exclusively.
	{
		if ( !$this->isAllowed( $parser->mTitle ) ) 
			return "<b>ForeachFunctions:</b> ".wfMsg('badaccess');
		
		$a = self::getArray( $object, $property, $param1, $param2 );
		
		if (empty( $a )) return;
		if (!is_array( $a ))
			$a = array( $a );
		
		$result = '';
		$index = 0;
		foreach( $a as $key => $value )
		{
			$result .= self::replaceVars( $pattern,  $key, $value, $index );
			$index++;
		}
		return $result;
	}
	/**
		Works on 'array' objects only.
	 */
	public function mg_foreachc( &$parser, &$X, &$Y, &$pattern, &$matchValue, &$matchValueReplacement )
	{
		if ( !$this->isAllowed( $parser->mTitle ) ) 
			return "<b>ForeachFunctions:</b> ".wfMsg('badaccess');
		
		$a = self::getArray( $X, $Y );
		
		if (empty( $a )) return;
		if (!is_array( $a ))
			$a = array( $a );
		
		$result = '';
		$index = 0;
		foreach( $a as $key => $value )
		{
			if ( $value == $matchValue )
				$match = $matchValueReplacement;
			else
				$match = null;
				
			$result .= self::replaceVars( $pattern,  $key, $value, $index, $match );
			$index++;
		}
		return $result;
	}
	public function mg_foreachy( &$parser, &$object, &$property, &$pattern, &$param1 = null, &$param2 = null )
	// {{#foreachy:global variable name||pattern}}	
	// {{#foreachy:global object name|property name|pattern}}
	// {{#foreachy:global object name|method name  |pattern}}	
	// Works on 'array' exclusively.
	{
		if ( !$this->isAllowed( $parser->mTitle ) ) 
			return "<b>ForeachFunctions:</b> ".wfMsg('badaccess');
		
		$a = self::getArray( $object, $property, $param1, $param2 );
		
		if (empty( $a )) return;

		if (!is_array( $a ))
			$a = array( $a );
			
		$result = '';
		foreach( $a as $index => $b )
			if (!empty( $b ))
				foreach( $b as $key => $value )
					$result .= self::replaceVars( $pattern,  $key, $value, $index );

		return $result;
	}


	public function mg_forx( &$parser, &$object, &$prop, &$pattern, &$start, &$stop )
	// {{#forx:global object name|property name|pattern|start index|stop index}}
	// {{#forx:global object name|method name  |pattern|start index|stop index}}	
	// Works on 'array' exclusively.
	{
		if ( !$this->isAllowed( $parser->mTitle ) ) 
			return "<b>ForeachFunctions:</b> ".wfMsg('badaccess');
		
		$a = self::getArray( $object, $prop );
		
		if (empty( $a )) return;
		
		if (!is_array( $a ))
			$a = array( $a );
		
		$result = '';
		for ( $index= $start; $index < $stop; $index++ )
		{
			$key = $index;
			$value = $a[ $key ];
			$result .= self::replaceVars( $pattern,  $key, $value, $index );
		}
			
		return $result;
	}

	private static function getArray( &$p1, &$p2, &$param1 = null, &$param2 = null )
	{
		$o = null;
		if (isset( $GLOBALS[$p1] ))
		{
			$o = $GLOBALS[$p1];

			if (is_array( $o ))
				if (!empty( $p2 ))
					return $o[$p2];
				else
					return $o;
	
			// array = object->property
			if (is_object( $o))
				if (is_array( $o->$p2 )) 
					return $o->$p2;
	
			// array = object->property()
			if (is_object($o))
				if (is_callable( array($o, $p2) ))
					return $o->$p2( $param1, $param2 );
		}
		
		// static property of a class?
		if (property_exists( $p1, $p2 ))
		{
			try
			{
				$vars = get_class_vars( $p1 );
				$val = $vars[ $p2 ];
			} 
			catch( Exception $e )
			{
				$val = null; 
			}
			return $val;
		}
		
		return null;		
	}
	
	public static function getType( &$p1 )
	{
		if (isset( $GLOBALS[$p1] ))
			return self::typeGLOBAL;

		if (is_array( $p1 ))
			return self::typeArray;

		if (is_object( $p1 ))
			return self::typeObject;

		if (class_exists( $p1 ))
			return self::typeClass;

		if (is_string( $p1 ))
			return self::typeString;
		
		return self::typeUnknown;
	}
	public static function replaceVars( &$pattern, &$key, &$value, &$index, &$match = null )
	{
		// find $key$ , $value$, $index$ variables in the pattern
		$r  = @str_replace( '$key$',   $key, $pattern );			
		$r2 = @str_replace( '$value$', $value, $r );
		$r3 = @str_replace( '$index$', $index, $r2 );
		$r4 = @str_replace( '$match$', $match, $r3 );			
	
		return $r4;
	}

	private function isAllowed( &$title )
	{ 
		if (self::$enableExemptNamespaces)
		{
			$ns = $title->getNamespace();
			if ( !empty(self::$exemptNamespaces) )
				if ( in_array( $ns, self::$exemptNamespaces) )
					return true;	
		}
		
		// check protection status
		if ( $title->isProtected( 'edit' ) ) return true;
		
		return false;
	}

} // end class.
//</source>