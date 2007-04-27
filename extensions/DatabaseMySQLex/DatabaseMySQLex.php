<?php
/*
 * DatabaseMysqlex.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a toolkit for easier Mediawiki
 *           extension development.
 *
 * FEATURES:
 * - 'singleton' implementation suited for extensions that require single instance
 * - 'magic word' helper functionality
 * - limited pollution of global namespace
 *
 * Tested Compatibility: MW 1.8.2 (PHP5), 1.9.3
 *
 * History:
 * v1.0		Initial availability
 * v1.01    Small enhancement in processArgList
 * v1.02    Corrected minor bug
 * v1.1     Added function 'checkPageEditRestriction'
 * v1.2     Added 'getArticle' function
 *
 */
$wgExtensionCredits['other'][] = array( 
	'name'    => '',
	'version' => '$LastChangedRevision: 53 $',
	'author'  => 'Jean-Lou Dupont', 
	'url'     => 'http://www.bluecortex.com',
);

class DatabaseMysqlex extends DatabaseMysql
{
	const thisName = 'DatabaseMySQLex';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	public function DatabaseMysqlex()
	{ 
		global $wgExtensionCredits;
		
		$wgExtensionCredits['other'][] = array(
		    'name'        => self::thisName,
			'version'     => '$LastChangedRevision: 53 $',
			'author'      => 'Jean-Lou Dupont [http://www.bluecortex.com]',
			'description' => 'Extends the standard DatabaseMysql class. '
		);

		global $wgHooks;
		$wgHooks['SpecialVersionExtensionTypes'][] = array( &$this, 'hUpdateExtensionCredits' );				
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
		{
			if ($el['name']==self::thisName)
				$el['description'].=" \$wgDBtype is set to <b>{$wgDBtype}</b> and \$wgDBclass is set to a <b>{$wgDBclass}</b>.";	
		}
	}
	
} // end class definition.
?>