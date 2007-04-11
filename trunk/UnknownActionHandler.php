<?php
/*
 * UnknownActionHandler
 * Mediawiki Extension
 *
 * @author: Jean-Lou Dupont (www.bluecortex.com)
 *
 * COMPATIBILITY:
 * - tested on Mediawiki v1.8.2
 *
 * Extension that provides a new hook "UnknownAction".
 *
 * This hook is triggered after a non-native action is requested
 * to Mediawiki. A custom [Action][Handler] associative array
 * is used to load a configured article page containing processing
 * code to handle the request.
 *
 * EXAMPLE USAGE:
 * ==============
 * A special page for editing a page's attributes made available
 * through the "PageAttributes" extension.
 * 
 * IMPORTANT USAGE NOTE:
 * =====================
 *
 * The Mediawiki page that stores the PHP code processor for the handler
 * must be structured in the following way:
 *
 * <php>
 *  function XYZ( &$article )
 *  { some code here }
 *
 *  return "XYZ";
 * </php>
 *
 * The extension leverages PHP's eval method capability to provide
 * a return code in order to get access to the method name to call.
 * 
 * DEPENDANCY:
 *  Requires the extension 'RunPHP Class' to operate.
 *
 * HISTORY:
 * V1.0    Initial availability.
 * V1.1    Integration with "RunPHP Class"
 * V1.2    Added Javascript integration.
 * V1.3    Changed to "silent" behavior when no handler present
 *         for an action.
 *
 */
$wgExtensionCredits['other'][] = array(
    'name'   => "UnknownActionHandler [http://www.bluecortex.com]",
	'version'=> "1.3",
	'author' => 'Jean-Lou Dupont' 
);

global $uaObj;
$uaObj = new UnknownActionHandler;

$wgHooks['UnknownAction'][] = array( $uaObj, 'hUnknownAction' );

class UnknownActionHandler
{
	var $handler = array();
	
	function UnknownActionHandler() {  }
	
	function setHandler( $action, $page ) { $this->handler[$action] = $page; }
	function getHandler( $action )        { return $this->handler[$action];  }
	function getHandlers()                { return $this->handler; }

	function hUnknownAction( $action, $article )
	{
		if (!isset($this->handler[$action]))
			return true; # means OK to continue looking for more handlers.

		# shortcut for title name
		$page_title = $this->handler[$action];
		
		// Check availability of "RunPHP Class"
		if (!class_exists('runphpClass'))
		{
			echo "Handler for $action action: missing runphpClass.";
			return false;
		}
		
		// Use our RunPHP class helper
		$runphp = new runphpClass;
		$runphp->init( $page_title );
		
		$code = $runphp->getCode();
		
		if ( empty($code) )
			return false;       # no code yet, return silently.
		
		#execute the code
		$callback = eval( $code );
			
		if (is_callable( $callback ) )
		  call_user_func( $callback, &$article );
		else
		{
		  echo "UnknownActionHandler: error in processor page code: callback function not found.";
		  return false;
		}

		if ( ($runphp->getType() & RUNPHPCLASS_COMPOSITE) == true)
		{
			global $wgOut;
			
			// Integrate Javascript code if any.
			$js = $runphp->getJsCode();
			if (!empty($js))
				$wgOut->addHTML("<script type='text/javascript'>".$js."</script>");
				
			// Finally, integrate any wikitext
			$content = $runphp->getContent();
			$wgOut->addWikiText( $content );
		}
				
		return false;	# means OK.
	}
}
?>
