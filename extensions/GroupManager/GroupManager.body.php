<?php
/**
 * @author Jean-Lou Dupont
 * @package GroupManager
 * @version $Id$
 */
//<source lang=php>
class GroupManager
{
	// 
	const thisType = 'other';
	const thisName = 'GroupManager';
	const rpage = 'Groups';

	// TABLE FORMATTING related
	static $columnSeparator = "||";
	static $rowStart = "|";
	static $rowEnd   = "|-";

	/**
	 * Parser Function
	 * {{#wggroup: group [ | rights | notes] }}
	 */
	public function mg_wggroup( &$parser, $groupname, $rights = null, $notes = null )
	{
		// parse the list and present the formatted version 
		$liste = $this->parseList( $rights, $rightsArray );

		static $index = 0;
		//	public function hRegistrySetPage( &$page, &$key, &$value )
		$page = self::rpage;
		
		// The RegistryManager can only store one serialized PHP variable per key...
		wfRunHooks( 'RegistryPageSet', 
			array( $page, $index++, array( 'g' => $groupname,  'r' => $rightsArray ) ));
		
	
		// Format a nice wikitext line
		return	self::$rowStart.
				$groupname.self::$columnSeparator.
				$liste.self::$columnSeparator.
				$notes."\r\n".
				self::$rowEnd."\r\n";
	}
	/**
	 * Parses the rights list to return a version of the said list
	 * formatted according to how it is going to be interpreted.
	 */
	public function parseList( &$liste, &$bits )
	{
		$bits = explode( ',', $liste );
		return implode( ',', $bits );
	}
	/**
	 * This hook is used to 'inject' the defined groups
	 * right before being needed e.g. Special:Userrights.
	 * We are not touching the current user's groups but
	 * only modifying the global $wgGroupPermissions array.
	 */
	public function hUserEffectiveGroups( &$user, &$groups )
	{
		global $wgGroupPermissions;
		
		$params = null;
		$page = self::rpage;
		wfRunHooks( 'RegistryPageGet', array( $page, &$params) );
		
		if (!empty( $params ))
			foreach( $params as $entry )
			{
				$groupName = $entry['g'];
				$rightsArray = $entry['r'];
				
				if (!empty( $rightsArray ))
					foreach( $rightsArray as $right )
						$wgGroupPermissions[ $groupName ][ $right ] = true;
			}
		return true;
	}

	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		$result1 =  "[http://mediawiki.org/wiki/Extension:RegistryManager RegistryManager] is ";
		$result1 .= (class_exists('RegistryManager')) ? 'present.':'absent: extension will not work.';
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1.'<br/>';
				
		return true; // continue hook-chain.
	}	
}
//</source>