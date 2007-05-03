<?php
/*
 * SysopToolBox.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a means of adding page links to the
 * ========  'toolbox' for the 'sysop' users.
 *           The page links are configured through:
 *           'MediaWiki:Sidebar/Sysop' 
 *
 * Features:
 * *********
 *
 * DEPENDANCY:  ExtensionClass extension (>v1.5)
 * 
 * Tested Compatibility:  MW 1.10
 * Patches for MW 1.8.x and MW 1.9.x available
 *
 * INSTALLATION NOTES:
 * -------------------
 * Add to LocalSettings.php
 *  require("extensions/ExtensionClass.php");
 *  require("extensions/SysopToolBox.php");
 *
 * History:
 * - v1.0
 *
 */

SysopToolBoxClass::singleton();

class SysopToolBoxClass extends ExtensionClass
{
	// constants.
	const thisName = 'SysopToolBox';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	const pageName = 'MediaWiki:Sidebar/Sysop';

	// variables.
	var $foundPage;

	public static function &singleton( ) // required by ExtensionClass
	{ return parent::singleton( ); }
	
	function SysopToolBoxClass()
	{
		parent::__construct(); 			// required by ExtensionClass

		global $wgExtensionCredits;
		$wgExtensionCredits['other'][] = array( 
			'name'        => self::thisName, 
			'version'     => 'v1.0 $LastChangedRevision: 53 $',
			'author'      => 'Jean-Lou Dupont', 
			'url'         => 'http://www.bluecortex.com',
			'description' => 'MediaWiki:Sidebar/Sysop page '
		);

		$this->foundPage = false;
	}
	public function setup() { parent::setup(); } // nothing special to do in this case.

	public function hSkinTemplateOutputPageBeforeExec( $skin, &$tpl )
	{
		// make sure we are dealing with a 'sysop' user.
		if (!$this->isSysop()) return true; // continue hook-chain
		
		$a = $this->getArticle(self::pageName);
		if (empty($a))
		{ 
			$this->foundPage = false;
			return true;
		}
		else $this->foundPage = true;
		
		$text = $a->getContents();
		$bar  = $this->processSidebarText( $text );
		
		// get current sidebar text
		$cbar = $tpl->data['sidebar'];
		
		// add our own here
		$tpl->set( 'sidebar', $cbar.$bar );		
		
		return true;
	}
	public function hUpdateExtensionCredits( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		if ($this->foundPage)
			$result = '<b>found</b>';
		else
			$result = '<b><i>not</i> found</b>';
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if ($el['name']==self::thisName)
				$el['description'].=$result;
				
		return true; // continue hook-chain.
	}
	private function processSidebarText( &$text )
	{
		$bar = array();
		$lines = explode( "\n", $text );
		foreach ($lines as $line) {
			if (strpos($line, '*') !== 0)
				continue;
			if (strpos($line, '**') !== 0) {
				$line = trim($line, '* ');
				$heading = $line;
			} else {
				if (strpos($line, '|') !== false) { // sanity check
					$line = explode( '|' , trim($line, '* '), 2 );
					$link = wfMsgForContent( $line[0] );
					if ($link == '-')
						continue;
					if (wfEmptyMsg($line[1], $text = wfMsg($line[1])))
						$text = $line[1];
					if (wfEmptyMsg($line[0], $link))
						$link = $line[0];

					if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $link ) ) {
						$href = $link;
					} else {
						$title = Title::newFromText( $link );
						if ( $title ) {
							$title = $title->fixSpecialName();
							$href = $title->getLocalURL();
						} else {
							$href = 'INVALID-TITLE';
						}
					}

					$bar[$heading][] = array(
						'text' => $text,
						'href' => $href,
						'id' => 'n-' . strtr($line[1], ' ', '-'),
						'active' => false
					);
				} else { continue; }
			}
		}
		return $bar;	
	}
} // END CLASS DEFINITION
?>