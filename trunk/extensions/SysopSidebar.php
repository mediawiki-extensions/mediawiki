<?php
/*
 * SysopSidebar.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * Purpose:  Provides a means of adding page links to the
 * ========  'sidebar' for the 'sysop' users.
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

SysopSidebarClass::singleton();

class SysopSidebarClass extends ExtensionClass
{
	// constants.
	const thisName = 'SysopToolBox';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	const pageName = 'MediaWiki:Sidebar/Sysop';

	// variables.
	var $foundPage;

	public static function &singleton( ) // required by ExtensionClass
	{ return parent::singleton( ); }
	
	function SysopSidebarClass()
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

	public function hSkinTemplateOutputPageBeforeExec( &$skin, &$tpl )
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
		
		$text = $a->getContent();
		$bar  = $this->processSidebarText( $text );
		
		// get current sidebar text
		$cbar = $tpl->data['sidebar'];

		// add our own here
		$tpl->set( 'sidebar', array_merge($cbar, $bar) );		
		
		return true;
	}
	private function processSidebarText( &$textSideBar )
	// copied from SkinTemplate MW 1.8.x SVN
	{
		$bar = array();
		$lines = explode( "\n", $textSideBar );
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
					$href = self::makeInternalOrExternalUrl( $link );
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
	static function makeInternalOrExternalUrl( $name )
	// copied from SkinTemplate MW 1.8.x SVN	 
	{
		if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $name ) ) {
			return $name;
		} else {
			return self::makeUrl( $name );
		}
	}

	static function makeUrl( $name, $urlaction = '' )
	// copied from SkinTemplate MW 1.8.x SVN 
	{
		$title = Title::newFromText( $name );
		self::checkTitle( $title, $name );
		return $title->getLocalURL( $urlaction );
	}

	static function checkTitle( &$title, &$name )
	// copied from SkinTemplate MW 1.8.x SVN 
	{
		if( !is_object( $title ) ) {
			$title = Title::newFromText( $name );
			if( !is_object( $title ) ) {
				$title = Title::newFromText( '--error: link target missing--' );
			}
		}
	}

} // END CLASS DEFINITION
?>