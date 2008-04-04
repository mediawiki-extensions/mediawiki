<?php
/**
 * @author Jean-Lou Dupont
 * @package FlowProcessor
 * @category Flow
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang='php'>
class FlowProcessor
{
	/**
	 * @constant
	 */
	const thisName = 'FlowProcessor';
	const thisType = 'other';
	
	/**
	 * Canonical namespace name
	 * @private
	 */
	static $_nsName = "Flow";
		
	/**
	 * @private
	 */
	static $_nsId = null;
	
	/**
	 * Constructor
	 */	
	public function __construct()
	{
		self::$_nsId = $this->getNsId();
	}
	/**
	 * Verifies if the required namespace is defined
	 * 
	 * @return $id integer
	 */
	protected function getNsId()
	{
		return Namespace::getCanonicalIndex( strtolower( self::$_nsName ) );	
	}	
	/**
	 * Verifies if the required Namespace is defined.
	 * 
	 * @return $result boolean
	 */
	protected function verifyNs()
	{
		return ( self::$_nsId !== null );
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// HOOK
	// ====
	
	/**
	 * Hook for SpecialPage list manipulation.
	 * Used to ''inject'' the special pages of the ''Flow'' namespace
	 * 
	 * @return $result boolean
	 * @param $liste Array
	 */
	public function hSpecialPage_initList( &$liste )
	{
		// Extract namespace & title
		global $wgTitle;
		
		// simple and maybe paranoid test...
		if ( !is_object( $wgTitle ))
			return true;
			
		$ns = $wgTitle->getNamespace();

		// Check if the request comes in the right namespace
		if ( $ns !== NS_SPECIAL )
			return true;

		$titleText = $wgTitle->getText();
	
		// Extract the $flow from the title
		$flowTitle = $this->extractFlowFromTitle( $titleText );
		
		// page name must start with "Flow"
		$baseId = ucfirst( @$flowTitle[0] );
		if ( $baseId !== self::$_nsName )
			return true;

		// Flow identifier comes next
		$flow = @$flowTitle[1];
		
		// just in case...
		if ( empty( $flow ))
			return true;
			
		// Is there a class available to handle the requested flow?
		if (  ( $classe = $this->checkClass( $flow ) ) === false )
			return true;
		
		// Format the page name as a function of the raw title / classe
		$page = $flow;
		
		// Insert in the list
		$liste[ $page ] = array( 'UnlistedSpecialPage', $classe );

		// continue hook-chain
		return true;
	}
	/**
	 * Sets up a factory that will be used to instantiate
	 * an object of class $classe
	 * 
	 * @return void
	 * @param $classe string
	 */
	protected function setupFactory( &$classe )
	{
		
	}
	/**
	 * Extracts the flow identifier from a page title
	 * E.g. title = Flow/Userlogin/signup
	 *      ==> flow = Userlogin
	 * 
	 * @return $flow array
	 * @param $title string
	 */
	protected function extractFlowFromTitle( &$title )
	{
		return explode( '/' , $title );
	}
	/**
	 * Verifies if the namespace corresponds to what we need
	 * 
	 * @return $index integer
	 * @param $nsName string
	 */	
	protected function checkNamespace( &$nsName )
	{
		return Namespace::getCanonicalIndex( $nsName );
	}
	/**
	 * Figures out if a class is available to handle
	 * a requested flow.
	 * In priority order:
	 * 0) In memory
	 * 
	 * 1) PEAR directory MediaWiki/Flows/$flow
	 *    filename is required to follow: MediaWiki/Flows/controller.php
	 *    
	 * 2) MW database with page = Flow:$flow
	 * 
	 * @return $class string
	 * @param $flow string
	 */	
	protected function checkClass( &$flow )
	{
		$classe = "MW_Flow_$flow";
		// check C0
		if ( class_exists( $classe ))
			return $classe;
			
		// check C1
		@include_once( $PEAR.'/'.$flow.'/controller.php' );
		if ( class_exists( $classe ))
			return $classe;

		// check C2 
		$code = null;
		$page = self::$_nsName.':'.$flow;
		if ( !$this->verifyFlowPage( $page, $code ) )
			return false;
		
		// instantite the class as to make it
		// readily available
		if ( !$this->prepareCode( $code ))
			return false;
			
		return $classe;
	}
	/**
	 * Instantiate the class
	 * 
	 * @return $result mixed
	 * @param $code string
	 * @todo Check if the code makes it in the bytecode cache
	 */	
	protected function prepareCode( &$code )
	{
		return eval( $code );		
	}
	/**
	 * @return $result boolean
	 * @param $page string
	 */
	protected function verifyFlowPage( &$page, &$code )	
	{
		$code = $this->getPage( $page );
		
		return ( empty( $code ) ? false:true );
	}
	/**
	 * Fetches a page ''raw'' content from the database
	 * 
	 * @return $content string
	 * @param $page string
	 */
	protected function getPage( $page )
	{
		$title = Title::newFromText( $page );
		if (!is_object( $title ))		
			return false;
			
		$contents = null;

		$rev = Revision::newFromTitle( $title );
		if( is_object( $rev ) )
		    $contents = $rev->getText();

		return $contents;
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// HELPERS
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	
	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		$result  = "Namespace <i>".self::$_nsName."</i> is ";
		$result .= ( $this->verifyNs() ? "available.": "<b>not available</b>." );

		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result.'<br/>';
				
		return true; // continue hook-chain.
	}	
	
	
} // END CLASS DEFINITION
//</source>