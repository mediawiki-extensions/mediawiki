<?php
/**
 * @author Jean-Lou Dupont
 * @package PageSidebar
 * @version 1.1.0
 * @Id $Id: PageSidebar.body.php 1189 2008-06-18 00:39:55Z jeanlou.dupont $
 */
//<source lang=php>
class PageSidebar
{
	const thisType = 'other';
	const thisName = 'PageSidebar';

	/*
	 * sidebar text in case
	 * no parser caching is in use
	 */
	var $sidebarText = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{}	 
	/**
	 * <pagesidebar> standard sidebar wikitext </pagesidebar>
	 */
	public function tag_pagesidebar( &$text, &$params, &$parser )
	{
		$trimmed_text = trim( $text );
		
		$processed_text = $this->processSidebarText( $trimmed_text );

		// serialize the array so we can easily store it
		// on the processed page.		
		$serialized_text = serialize( $processed_text );
		
		// encode the string as to make sure we have no clashes
		// with the HTML of the page.
		$encoded_text = base64_encode( $serialized_text );
		
		$output = "<!--sidebar--${encoded_text}--sidebar-->";
	
		// keep the text in case no parser caching is in effect
		$this->sidebarText = $processed_text;
		
		return $output;
	}
	/**
	 * extractSidebarText
	 * Extracts the 'sidebar' section embedded in the HTML
	 * comment and decodes it.
	 */
	protected function extractSidebarText( &$text )
	{
		// only looking for 1 section
		$result = preg_match('/<!--sidebar--(.*)--sidebar-->/si', $text, $match );
		if ( $result !== 1 )
			return null;
			
		$serialized_text = base64_decode( $match[1] );
		$this->sidebarText = unserialize( $serialized_text );
	}

	/*==========================================================================
	 * HOOKS
	 ==========================================================================*/
	/**
	 * [[Extension:SidebarEx]] calls this hook
	 */
	public function hPageSidebar( &$content ) {
		
		$content = $this->sidebarText;
		
		return true;
	}
	
	/**
	 * Handler for OutputPageParserOutput
	 * NOTE: this method is called even if parser caching 
	 *       is turned off.
	 * 
	 * @param $op OutputPage Object
	 * @param $parserOutput Object
	 */
	public function hOutputPageParserOutput( &$op, $parserOutput )
	{
		// maybe parser caching is really in effect?
		if ( $this->sidebarText === null )
		{
			$text = $parserOutput->getText();
			
			$this->extractSidebarText( $text );
		}
		
		return true;
	}
	/**
	 * Handler for SkinTemplateOutputPageBeforeExec
	 * 
	 * @param $skin Skin Object
	 * @param $tpl SkinTemplate Object
	 */
	public function hSkinTemplateOutputPageBeforeExec( &$skin, &$tpl )
	{
		// if [[Extension:SidebarEx]] is present, then
		//  don't bother trying to modify the sidebar
		if ( class_exists( 'SidebarEx') ) {
			return true;
		}
		
		// make sure we have something to add
		if ( $this->sidebarText === null )
			return true;
			
		// get current sidebar text
		$cbar = $tpl->data['sidebar'];

		// add our own here
		$tpl->set( 'sidebar', array_merge($cbar, $this->sidebarText ) );		
		
		return true;
	}
	
	/*==========================================================================
	 * HELPERS
	 ==========================================================================*/
	
	/**
	 * processSidebarText
	 * NOTE: copied from SkinTemplate MW 1.8.x SVN
	 * 
	 * @return array
	 * @param $textSideBar string
	 */
	private function processSidebarText( &$textSideBar )
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
	/**
	 * makeInternalOrExternalUrl
	 * NOTE: copied from SkinTemplate MW 1.8.x SVN	 
	 */
	private static function makeInternalOrExternalUrl( $name )
	{
		if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $name ) ) {
			return $name;
		} else {
			return self::makeUrl( $name );
		}
	}
	/**
	 * makeUrl
	 * NOTE: copied from SkinTemplate MW 1.8.x SVN	 
	 */
	private static function makeUrl( $name, $urlaction = '' )
	{
		$title = Title::newFromText( $name );
		self::checkTitle( $title, $name );
		return $title->getLocalURL( $urlaction );
	}

	/**
	 * checkTitle
	 * NOTE: copied from SkinTemplate MW 1.8.x SVN	 
	 */
	private static function checkTitle( &$title, &$name )
	{
		if( !is_object( $title ) ) {
			$title = Title::newFromText( $name );
			if( !is_object( $title ) ) {
				$title = Title::newFromText( '--error: link target missing--' );
			}
		}
	}

} // end class
//</source>
