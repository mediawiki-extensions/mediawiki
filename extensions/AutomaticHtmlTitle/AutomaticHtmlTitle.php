<?php
/**
 * @package ExtensionManager
 * @category PageMetaData
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {

	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:AutomaticHtmlTitle'>ExtensionManager</a>";
	die(-1);
	
}

class MW_AutomaticHtmlTitle 
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME    = 'automatichtmltitle';
	
	/** 
	 * Page-scope variables passed
	 * through the hook 'automichtmltitle'
	 */
	static $_vars = array();
	
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
		
		parent::__construct();

	}
	/**
	 * Optional setup: called once it is safe
	 *  to perform additional setup on the MediaWiki platform.
	 * 
	 * @optional This method can be omitted.
	 */
	protected function setup(){
		
		$this->setCreditDetails( array( 
			'name'        => $this->getName(), 
			'version'     => self::VERSION,
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Provides automatic HTML title element generation in the HEAD section',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:AutomaticHtmlTitle',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	
	/**
	 * Handler for the hook 'AutomaticHtmlTitle'
	 */
	public function hook_AutomaticHtmlTitle( $varName, $value ) {
	
		self::$_vars[ $varName ] = $value;
		
		return true;
	}
	
	/** 
	 * Handler for "BeforePageDisplay" HOOK
	 * @param $op object OutputPage
	 * @param $text string page text
	 * @return standard MW hook result
	 */
	public function hook_BeforePageDisplay( &$op ) {
	
		global $wgTitle;
		
		$tpl = $this->getTemplate( $wgTitle );
		if ( is_null( $tpl ) )
			return true;
		
		$this->replaceVars( $tpl, self::$_vars );
			
		$result = $this->parseTemplate( $tpl, $wgTitle );
		
		$this->cleanUp( $result );
		
		// finally, insert as HEAD element
		$op->setHTMLTitle( $result );
		
		// continue hook-chain
		return true; 
	}
	/**
	 * Clean-up the unnecessary HTML markup 
	 * produced by the parser.
	 * @param $text string
	 */
	protected function cleanUp( &$text ) {
		$text = str_replace( '<p>', '', $text );
		$text = str_replace( '</p>', '', $text );
		$text = str_replace( '<br/>', '', $text );
		$text = str_replace( "\n", '', $text );		
	}
	/**
	 * PARSES the template as if it was
	 * a wikitext page.
	 */
	protected function parseTemplate( &$tpl, &$title ) {
	
		// reuse the global parser
		// At this point in the page processing, it does 
		// not really matter anyways.
		global $wgParser;
		
		$po = $wgParser->parse( $tpl, $title, new ParserOptions );
		
		return $po->getText();
	
	}
	/** 
	 * REPLACE the dynamically registered variables
	 * @param $tpl string template
	 * @param $vars array of key=>value variables
	 */
	protected function replaceVars( &$tpl, &$vars ) {
	
		if (empty( $vars ))
			return;
	
		foreach( $vars as $key => &$value )
			$tpl = str_replace( $key, $value, $tpl );
	
	}
	
	/**
	 * GETS a template as a function of the 
	 * current's title namespace. 
	 */
	protected function getTemplate( &$title ) {

		// prepare in case we are facing a 'special page'
		$suffix = null;
		
		// are we in the 'Special' namespace?
		$name = strtolower( $title->getText() );		
		$nsId = $title->getNamespace();
		if ( NS_SPECIAL === $nsId )
			$suffix = '/' . $name ;
		
		//extract namespace-scoped template identifier

		$ns   = $title->getNsText();
		$ns   = strtolower( $ns );
		$ns   = empty( $ns ) ? "main" : $ns;  #for the main namespace case  
		
		// template identifier in the MediaWiki namespace
		$tplId= self::NAME . '-nstpl-' . $ns . $suffix ;
		
		$tpl =  $this->getMessage( $tplId );
		
		return $this->extractRelevantContent( $tpl );
	}
	/**
	 * GETS a message from the MediaWiki namespace
	 */
	protected function getMessage( &$key ) {

		$source = wfMsgGetKey( $key, true, true, false );
		if ( wfEmptyMsg( $key, $source ) )
			return null;
		
		return $source;
	}
	/**
	 * Extracts the relevant content from the template page
	 * i.e. removes the <noinclude> section
	 */
	protected function extractRelevantContent( &$text ) {
		
		return preg_replace( '/\<noinclude\>(.*)\<\/noinclude\>/siU', '', $text );
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_AutomaticHtmlTitle;

#include 'AutomaticHtmlTitle.i18n.php';
