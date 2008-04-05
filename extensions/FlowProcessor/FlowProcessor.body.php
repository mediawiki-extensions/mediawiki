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
	
	static $_PEAR = "MediaWiki/Flows";
	
	/**
	 * Canonical namespace name
	 * ucfirst!
	 * 
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
	// MAIN HOOK
	// =========
	
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
		
		// Flow identifier comes next
		$flow = @$flowTitle[0];
	
		// just in case...
		if ( empty( $flow ))
			return true;
			
		// Is there a class available to handle the requested flow?
		if (  ( $classe = $this->checkClass( $flow ) ) === false )
			return true;
		
		// Format the page name as a function of the raw title / classe
		$page = self::$_nsName.$flow;
		
		// Insert in the list
		$liste[ $page ] = array( 'UnlistedSpecialPage', $page );

		// continue hook-chain
		return true;
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
		$bits = explode( '/' , $title );
		
		// FlowXYZ ...
		$bit0 = ucfirst( @$bits[0] );
		
		array_shift( $bits );
		
		if ( empty( $bit0 ))
			return null;

		// make sure we have "Flow" for starters
		$len = strlen( self::$_nsName );
		$baseId = substr( $bit0, 0, $len );
		$flowId = substr( $bit0, $len );
		
		if ( $baseId !== self::$_nsName )
			return null;
		
		array_unshift( $bits, $flowId );		
		
		return $bits;
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
		$path = self::$_PEAR.'/'.$flow.'/controller.php';
		@include_once( $path );
		if ( class_exists( $classe ))
			return $classe;

		// check C2 
		$code = null;
		$page = self::$_nsName.':'.$flow;
		if ( !$this->verifyFlowPage( $page, $contents) )
			return false;
			
		$code = $this->extractCodeFromContents( $contents );
		
		// instantite the class as to make it
		// readily available
		$this->prepareCode( $code );
			
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
	 * The page must be ''edit protected'' for security reasons
	 * 
	 * @return $content string
	 * @param $page string
	 */
	protected function getPage( $page )
	{
		$title = Title::newFromText( $page );
		if (!is_object( $title ))		
			return false;

		if ($title->isProtected('edit'))
			return false;
			
		$contents = null;

		$rev = Revision::newFromTitle( $title );
		if( is_object( $rev ) )
		    $contents = $rev->getText();

		return $contents;
	}
	/**
	 * Extracts the PHP code from the $contents
	 * The code can either be 'straight' in the page or
	 * enclosed in a <source> tag section, the latter
	 * having priority.
	 * 
	 * @return $code string
	 * @param $contents string
	 */
	protected function extractCodeFromContents( &$contents )	
	{
		// get rid of PHP opening tag
		$contents = str_replace( '<?php', '', $contents );

		$result = preg_match( "/<source(?:.*)>(.*)<\/source>/siU" , $contents, $match );
		if ( $result === 1 )
			return $match[1];
		
		return $contents;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Special:Version helper
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