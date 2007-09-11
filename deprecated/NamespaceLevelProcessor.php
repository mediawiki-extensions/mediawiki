<?php
/*
 * NamespaceLevelProcessor.php
 * Mediawiki Extension
 *
 * @author: Jean-Lou Dupont (www.bluecortex.com)
 *
 * COMPATIBILITY:
 * - tested on Mediawiki v1.8.2, 1.9.3
 *
 * Extension that provides the capability to execute PHP code (on a
 * stored Mediawiki page) for each page on a per namespace level.
 *
 * INSTALLATION:
 * =============
 *
 * Use the global variable defined in this extension ($nlpHandlers)
 * to associate a PHP processor to any number of namespaces in a project.
 * This configuration should be put in the project's ''LocalSettings.php''.
 *
 * E.g.
 *      global $nlpHandlers;
 *      $nlpHandlers[100] = 'Admin:Ns100Processor';      # use this wikipage
 *    OR 
 *      $nlpHandlers[NS_MAIN] = 'Admin:MainNsProcessor'; # defined in MW 'Defines.php'
 *                                                       # & MW 'Namespace.php'
 *
 * Optionally, a page level processor can be assigned through the 
 * 'page_processor' "page attribute". This functionality is only available when the 
 * "PageAttributes" extension is available. 
 *
 * DEPENDANCIES:
 * =============
 *
 * - This extension depends on 'RunPHP Class' extension to operate.
 * - Optionally, the extension relies on "PageAttributes" extension
 *   to fetch a 'page processor' attribute.
 *   If this option is to be used, please load the extension "PageAttributes"
 *   before this one i.e. before in the "LocalSettings.php" file.
 * - RunPHP Class extension 
 *
 * FEATURES:
 * =========
 * 
 * - No MW code change
 * - Provides per-namespace PHP processing
 * - Optionally provides page level PHP processing
 * - Optionally provides a "default processor" applicable to ALL namespaces
 * - Precedence is given to page level processors (even in the default case)
 * - PHP processor code is stored in the standard MW database
 *
 * HISTORY:
 * V1.0    Initial availability.
 * V2.0    - Added some safety checks (Runphp Page availability)
 *         - Added integration with "PageAttributes" in order
 *           to implement 'page level' page processing.
 *
 * V2.1    - Added support for "composite page" 
 *           (i.e. page which contains both "wikitext" for presentation
 *            and PHP code for processing) Changes to 
 *            limited to ProcessPage function.
 *         - Additional dependancy on RunPHP Class extension
 *
 * V2.2	   - Corrected "composite page" bug
 *         - Removed superfluous check for "runphpExecCode"
 *         - Added the capability to disable processing on a page level.
 *
 * V2.3	   - Corrected return value for integration with
 *           other hooks on "ArticleAfterFetchContent"
 *
 * V2.4		-a) Added the capability to process a composite page
 *              that does not include a <PHP> section.
 *          -b) Added the capability to skip executing the composite page
 *              part of the processing.
 *          -c) Integration with 'ArticleExClass' extension:
 *              only execute the namespace level processor script upon
 *              'first article load' during a client transaction i.e.
 *              check if the article being fetched is the first of a
 *              client transaction: if YES, then continue processing
 *              else stop processing.
 *              This feature is useful when integrating with other extensions
 *              such as 'HeaderFooter".
 *          -d) Added the content returned from the composite page to the
 *              callback method.
 *
 * V2.5     - Added check to make sure that the processor is only called once
 *            per transaction.
 * v2.6     -a) (minor) added initialisation for "$skipCompositePageProc" variable
 *            for additional robustness (even though it gets a 'null' value by default)
 *          -b) (minor) added check for empty code
 *          -c) (major) added 'magic word' __PAGECONTENT__ processing whereby if this
 *              magic word appears in the composite page, then the current article's
 *              content is inserted in the composite page.
 *          -d) Changed ProcessPage to non-static function definition.
 */
$wgExtensionCredits['other'][] = array(
    'name'   => "NamespaceLevelProcessor [http://www.bluecortex.com]",
	'version'=> '$LastChangedRevision$',
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);
  
 # Global array used to configure
 # the 'processors'.
 global $nlpHandlers, $nlpDefaultProcessorEnable, $nlpDefaultProcessor;
 $nlpHandlers = array();
 
 # Configuration for the Default Processor
 # when none is configured for a Namespace.
 $nlpDefaultProcessorEnable = false;
 $nlpDefaultProcessor       = null;  #override this variable 

$nlpObj = new NamespaceLevelProcessor;
$wgHooks['ArticleAfterFetchContent'][]       = array( $nlpObj, 'hNamespaceLevelProcessor' );

// Because of how Mediawiki loads its components,
// we need to initialise the message cache this way.
$wgExtensionFunctions[] = 'NamespaceLevelProcessorSetup';
 
function NamespaceLevelProcessorSetup()
{
	global $wgMessageCache;
	$wgMessageCache->addMessage( 'nlpCallbackError' ,   'NamespaceLevelProcessor: failed to execute callback.' ); 
	$wgMessageCache->addMessage( 'nlpRunPHPClassError',	'NamespaceLevelProcessor: Missing runphpClass class definition.'); 
}

class NamespaceLevelProcessor
{
	public static $procAttr    = "page_processor";
	public static $disableAttr = "disable_processor";
	
	var $inproc;  # re-entrancy flag;
	var $dis;
	var $completed;
	
	function NamespaceLevelProcessor() 
	{ 
		$this->inproc    = false;
		$this->dis       = false; // assume enable state.
		$this->completed = false;
	}
	function hNamespaceLevelProcessor( &$article, &$content )
	{
		if ($this->completed)
			return true;
		$this->completed = true; // V2.5
		
		global $action;
		if ($action != 'view')
			return true;
					
		// v2.4c change
		// Check if first article in transaction
		if (get_class($article)=='ArticleExClass')
			if (!$article->getA('first'))
				return true;	
					
		// Re-entrancy check
		// If this function is called recursively,
		// that probably means a processor code page 
		// is being fetched. Get out.
		if ($this->inproc)
			return true; 
		
		$this->inproc= true;		
		
		$pp = null;
		
		# get the namespace of this article.
		$ns = $article->mTitle->getNamespace();
		
		# Look-up if we have a page associated with this namespace.
		global $nlpHandlers, $nlpDefaultProcessor, $nlpDefaultProcessorEnable;
		
		// 1st, verify if the "PageAttributes" extension is available.
		//
		$pp = $this->GetPageLevelProcessor( &$article );
		
		// are we asked to disable processing for this page?
		if ($this->dis)
			return true;
		
		// If no valid page_attribute processor found,
		// then continue looking.
		if ( ($pp==null) || ($pp=='') )
		{			
			// Look for namespace level processor
			if (isset($nlpHandlers[$ns]))
				$pp = $nlpHandlers[$ns];
			else 
			{
				// do we have a default handler setup?
				if ( $nlpDefaultProcessorEnable )
					$pp = $nlpDefaultProcessor;
				else
					$pp = null;  #fall through silently.
			}
		}		
		
		// If we were lucky, then try to execute the processing code.
		if ( ($pp!==null) || ($pp!='') )
			$this->ProcessPage( &$article, &$content, $pp ); // v2.6d change
			
		// RESET re-entrancy flag.
		$this->inproc= false;
		
		return true;
	} #end handler

	function GetPageLevelProcessor( &$article )
	{
		$page_title = null;
		
		if ( class_exists('PageAttributes') )
		{
			$paobj   = PageAttributes::getGlobalObject();
			
			// Get 'page_processor' value attribute IF it exists
			$aid = $article->getID();
			$page_title  = $paobj->getAttribute( $aid, self::$procAttr    );
			$this->dis   = $paobj->getAttribute( $aid, self::$disableAttr ); 
		}
		
		return $page_title;
	}
	
	private function ProcessPage( &$article, &$content, $page_title )
	{
		global $wgOut;
		global $wgParser;
		
		$wgParser->disablecache();
		
		// Check availability of "RunPHP Class"
		if (!class_exists('runphpClass'))
		{
			$wgOut->addWikiText( wfMsg('nlpRunPHPClassError') );
			return;
		}
			
		// Use our runphpClass helper
		$runphp = new runphpClass;
		$runphp->init( $page_title );	
						
		// Now, we support 2 basic cases:
		// 1- Composite Page
		// 2- PHP section & PHP only
		// All cases REQUIRED a valid callback function name to be
		// returned when the base code is loaded in the PHP machine.
		$code = $runphp->getCode( true ); // v2.4 change, asked for only
											// code enclosed in PHP section.
		
		# V2.4 change.
		#if ( empty($code) )
		#	return;       # no code yet, return silently.

		# next, load the code in the PHP machine and get return value
		$callback = null;   // v2.6b change
		if (!empty($code))  // v2.6b change
			$callback = eval( $code );
		
		// Next, we need to take different steps depending on the
		// page type we were pointed to.
		// Case 1: Composite Page.
		//         a) Load the composite page's content in this page content
		//         b) Execute the callback with, as parameter, the current's page content
		//
		//         The case of the composite page is interesting as the "presentation"
		//         layer can be written in wikitext and the processing heavy lifting
		//         done by the PHP code passing results through things like "variables" 
		//         extensions.
		//
		// Case 2: PHP only or PHP section page.
		//         just step b) above.
		// ==============================================================================
		
		if ( ($runphp->getType() & RUNPHPCLASS_COMPOSITE) == true )
			$cpcontent = $runphp->getContent();
		else
			$cpcontent = null;

		$skipCompositePageProc = false; // v2.6a -- by default, we are including the composite page.
		
		// v2.4d change: added content from composite page.
		if ( is_callable( $callback ) )
		  $skipCompositePageProc = call_user_func( $callback, &$article, &$content, &$cpcontent );
		  // V2.4 change.
		  // Issue error if there was some PHP code to execute BUT
		  // without a valid callback method.
		elseif (!empty($code))
		  $wgOut->addWikiText( wfMsg('nlpCallbackError') );
		 
		// Finally, if we were dealing with a composite page,
		// stick the composite page's wikitext in front.
		if ((($runphp->getType() & RUNPHPCLASS_COMPOSITE) == true) && !$skipCompositePageProc) // v2.4 b)
			$this->processCompositePage( $cpcontent, $content ); // v2.6c
		
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
  	}
	private function processCompositePage( &$cpcontent, &$content ) // v2.6c
	{
		$p = "/__PAGECONTENT__/si";
		$r = preg_match( $p, $cpcontent );
		
		if ($r > 0) // did we find the 'magic word' ?
			$content = trim(preg_replace( $p, $content, $cpcontent ));
		else
			$content = trim($cpcontent);	
	}
} #end class
?>