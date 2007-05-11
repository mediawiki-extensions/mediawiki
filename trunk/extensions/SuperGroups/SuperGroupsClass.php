<?php
/*
 * SuperGroupsClass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 */
class SuperGroupsClass extends ExtensionClass
{
	static $name = 'SuperGroups';
	static $type = 'other';
		
	public static function &singleton( )
	{ return parent::singleton( ); }
	
	// Our class defines magic words: tell it to our helper class.
	public function SuperGroupsClass() 
	{
		return parent::__construct( );
	}
	


} // end class definition.
?>