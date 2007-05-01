<?php
/*
 * DatabaseMysqlex.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * History:
 * ========
 *
 */

class DatabaseMySQLex extends DatabaseMysql
{
	const thisName = 'DatabaseMySQLex';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	public function __construct( $server, $user, $password, $dbName, $failFunction, $flags )
	{ 
		global $wgExtensionCredits;
		
		$wgExtensionCredits['other'][] = array(
		    'name'        => self::thisName,
			'version'     => '$LastChangedRevision$',
			'author'      => 'Jean-Lou Dupont [http://www.bluecortex.com]',
			'description' => 'Extends the standard DatabaseMysql class. '
		);

		return parent::__construct( $server, $user, $password, $dbName, $failFunction, $flags );			
	}
###################################################################################
/*
    New Methods
*/
###################################################################################
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits, $wgDBclass, $wgDBtype;
	
		if (!isset( $wgDBclass)) return;
			
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if ($el['name']==self::thisName)
				$el['description'].=" \$wgDBtype is set to <b>{$wgDBtype}</b> and \$wgDBclass is set to a <b>{$wgDBclass}</b>.";	
	}

###################################################################################
/*
    Overloaded Methods
*/
###################################################################################

	function makeList( $a, $mode = LIST_COMMA ) 
	{
		#var_dump( $a );
		#echo "<br/>";
		
		// Check for 'page_namespace' condition
		
			
		return parent::makeList( $a, $mode );
	}

} // end class definition.
?>