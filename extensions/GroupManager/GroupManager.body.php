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
		{{#wggroup: group [| notes] }}
	 */
	public function mg_wggroup( &$parser, $groupname, $notes = null )
	{
		static $index = 0;
		//	public function hRegistrySetPage( &$page, &$key, &$value )
		wfRunHooks( 'RegistryPageSet', self::rpage, $index++, $groupname );
		
		// Format a nice wikitext line
		return	self::$rowStart.
				$groupname.self::$columnSeparator.
				$notes."\r\n".
				self::$rowEnd."\r\n";
	}
	/**
	 *
	 */
	public function hUserEffectiveGroups( &$user, &$groups )
	{
		$params = null;
		wfRunHooks( 'RegistryPageGet', self::rpage, $params );
		if (!empty( $params ))
			$groups = array_merge( $groups, $params);
		return true;
	}

	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		$result1 =  "[http://mediawiki.org/wiki/Extension:RegistryManager RegistryManager] is ";
		$result1 .= (class_exists('RegistryManager')) ? 'present.':'absent.';
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1.'<br/>';
				
		return true; // continue hook-chain.
	}	
}
//</source>