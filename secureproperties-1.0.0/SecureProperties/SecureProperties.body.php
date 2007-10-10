<?php
/**
	@author Jean-Lou Dupont
	@package SecureProperties
 */
//<source lang=php>
class SecureProperties
{
	// constants.
	const thisName = 'SecureProperties';
	const thisType = 'other';
		
	const actionGet = 0;
	const actionSet = 1;
	const actionGGet = 2;
	const actionGSet = 3;
	const actionFnc = 4;	
	const actionCGet = 5;
	const actionCSet = 5;	
	
	const gobject   = 0;
	const gvariable = 1;
	const gclass    = 3;
	
	// Namespace exemption functionality
	static $enableExemptNamespaces = true;
	static $exemptNamespaces = array();
	
	public static function addExemptNamespaces( $list )
	{
		if (!is_array( $list ))	
			$list = array( $list );
			
		self::$exemptNamespaces = array_merge( self::$exemptNamespaces, $list );
	}
	
	function __construct()
	{
		// default exempt namespaces from the BizzWiki platform.
		// won't affect installs of the extension outside the BizzWiki platform.
		if (defined('NS_BIZZWIKI'))   self::$exemptNamespaces[] = NS_BIZZWIKI;
		if (defined('NS_FILESYSTEM')) self::$exemptNamespaces[] = NS_FILESYSTEM;
	}

	public function mg_pg( )
	// {{#pg:object|property}}
	// (($#pg|object|property$))
	{
		$args = func_get_args();
		return $this->process( $args, self::actionGet );
	}

	public function mg_ps( )
	// {{#ps:object|property name|value}}
	// (($#ps|object|property|value$))	
	{
		$args = func_get_args();
		return $this->process( $args, self::actionSet );
	}
	public function mg_pf( )
	// {{#pf:object|function name}}
	// (($#pf|object|function name$))	
	{
		$args = func_get_args();
		return $this->process( $args, self::actionFnc );
	}
	public function mg_gg( )
	// {{#gg:global variable}}
	// (($#gg|global variable$))
	{
		$args = func_get_args();
		return $this->process( $args, self::actionGGet, self::gvariable );
	}
	public function mg_gs( )
	// {{#gs:global variable}}
	// (($#gs|global variable|value$))
	{
		$args = func_get_args();
		return $this->process( $args, self::actionGSet, self::gvariable  );
	}

	public function mg_cg( )
	// {{#cg:class name|property}}
	// (($#cg|class name|property$))
	{
		$args = func_get_args();
		return $this->process( $args, self::actionCGet, self::gclass );
	}
	public function mg_cs( )
	// {{#cs:class name|property|value}}
	// (($#cs|class name|property|value$))
	{
		$args = func_get_args();
		return $this->process( $args, self::actionCSet, self::gclass  );
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	private function process( &$args, $action = self::actionGet, $type = self::gobject )
	{
		$parser = @$args[0];
		
		if ( !$this->isAllowed( $parser->mTitle ) ) 
			return "<b>SecureProperties:</b> ".wfMsg('badaccess');

		$object   =             @$args[1];
		$property = $fnc      = @$args[2];
		$value    = $param1   = @$args[3];
		$param2               = @$args[4];
		$param3               = @$args[5];
				
		if ($type == self::gobject)
			if ( !is_object( $obj = $GLOBALS[$object] ) ) 
				return "<b>SecureProperties:</b> ".wfMsg('error')." <i>$object</i>";

		if ($type == self::gvariable)
			if ( !isset( $GLOBALS[$object] ) ) 
				return "<b>SecureProperties:</b> ".wfMsg('error')." <i>$object</i>";

		if ($type == self::gclass)
			if ( !class_exists( $object ) ) 
				return "<b>SecureProperties:</b> ".wfMsg('error')." <i>$object</i>";

		switch( $action )
		{
			case self::actionGet:
				return $obj->$property;
			case self::actionSet:
				$obj->$property = $value;					
				return null;
			case self::actionFnc:
				return $obj->$fnc();
			case self::actionGGet:
				return $GLOBALS[ $object ];
			case self::actionGSet:
				$GLOBALS[ $object ] = $property;
				return null;
			case self::actionCGet:
				return eval("return $object::$property;");
			case self::actionCSet:
				eval("$object::$property = $value;");
				return null;
		}
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

} // end class
//</source>