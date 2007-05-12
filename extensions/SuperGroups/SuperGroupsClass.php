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
	const thisName = 'SuperGroups';
	const thisType = 'other';  

	const nssSize  = 256;  // namespace set size

	static $tableName = 'supergroups';
	static $errId;
		
	// 
	var $sgid;
	
	// We need to be sure we are getting initialized one of the first:
	// that's why we are setting 'true' for $initFirst parameter	
	public static function &singleton( )
	{ return parent::singleton( null , null, self::mw_style, 1, true); }
	
	public function SuperGroupsClass( $mgwords, $passingStyle, $depth, $initFirst) 
	{		return parent::__construct( $mgwords, $passingStyle, $depth, $initFirst );	}
	
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		// check first for the existence of the required database table.
		$dbr = wfGetDB(DB_SLAVE);
		$r   = $dbr->tableExists(self::$tableName);

		if ( $r ) $result1 = '<b><i>supergroups</i> database table found</b>';
		else      $result1 = '<b><i>supergroups</i> database table NOT found</b>';
		
		// then let's check if we had errors in the $sgExtraNamespace array
		if (self::$errId) $result2 = '<b><i>$sgExtraNamespaces</i> array appears OK</b>';
		else              $result2 = '<b><i>$sgExtraNamespaces</i> array has errors</b>';
		
		$this->updateCreditsDescription($result);
		
		return true; // continue hook-chain.
	}

	public function setup()
	{
		parent::setup();
		
		self::$errId = false;
		$this->sgid = 0; // assume default.
		
		// namespace boundary checks
		// only allowed 256 namespaces per SuperGroup
		$r = $this->checkBoundaries();
		
		// if we got a 'null', then not much to do!
		// (i.e. there is no 'extra' namespaces asked for)
		if ($r == null) return;
		
		// if we got 'false', then an error with the
		// namespace ids was declared.
		if ( $r == false )
		{	self::$errId = true; return; }
		
		// if we got a 'true' response, then let's setup!
		$this->setupExtraNamespaceSet();
		
	}
	private function checkBoundaries()
	{
		global $sgExtraNamespaces;
		
		if ( !isset($sgExtraNamespaces) || empty($sgExtraNamespaces) )
			return null;
		
		// each 'id' must be between >=0 and <=255
		// to be valid.
		foreach( $sgExtraNamespaces as $id => &$el )
			if ( ( $id < 0) || ( $id > 255) )
				return false;
				
		return true;
	}

	private function setupExtraNamespaceSet()
	{
		global $sgExtraNamespaces;
		global $wgExtraNamespaces;
		
		// for each extra namespace we need to
		// to use 'supergroup' identifier to derive the 'base'
		// namespace set offset.
		foreach( $sgExtraNamespaces as $id => &$el )
		{
			$nid = ($this->sgid*self::nssSize) + $id;
			define( $el['id'], $nid );
			$wgExtraNamespacesp[$nid] = $el['name'];
		}
	}

} // end class definition.
?>