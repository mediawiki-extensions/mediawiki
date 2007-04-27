<?php
/*
 * AfterEdit
 * $LastChangedRevision$
 *
 * Mediawiki Extensions
 *
 * @author: Jean-Lou Dupont (www.bluecortex.com)
 *
 * COMPATIBILITY:
 * - tested on Mediawiki v1.8.2
 *
 * Extension that provides a new hook "AfterEdit".
 * This hook is triggered after article edition / submitting
 * is completed but before control is returned to the normal
 * work flow whereas the resulting article would be displayed
 * through the OutputPage object.
 *
 * This extension is useful for manipulating the OutputPage object
 * before a page is rendered & sent to the client browser.
 *
 * An example of what can be done:  changing the "redirect" link
 * to point to another page upon successful edition of a page. This
 * can be handy when forms are handled: a user submits a page to
 * MW and once the page is stored, a "Thank you for your submission"
 * page can be pointed to *instead* of the newly created page.
 *
 * NOTE: this extension should be the first loaded in the chain
 *       of other extensions hooked on "AlternateEdit".
 * 
 * WARNING: There is a potential for other extensions to get
 *          called more than once if the AfterEdit extension is
 *          not loaded first. E.g. an extension might be called
 *          through the usual MW work flow AND THEN be called
 *          once more by this extension. 
 */

$wgExtensionCredits['other'][] = array(
    'name' => 'AfterEdit',
    'description' => "adds a new ''AfterEdit''[http://www.bluecortex.com] hook",
	'author' => 'Jean-Lou Dupont' 
);


global $aeObj;
$aeObj = new AfterEdit;

$wgHooks['AlternateEdit'][] = array( $aeObj, 'hAlternateEdit' );

class AfterEdit
{
	// Re-entrance flag.
	var $inproc;
	
	function AfterEdit() { $this->inproc = false; }

	function hAlternateEdit( &$eobj )
	{
		if ($this->inproc)
			return true; # allow other extensions a chance to execute.
		
		# first off, disable AlternateEdit hook 
		# or else some serious reentrance issue!
		$this->inproc= true;
		
		# Substitute the standard EditPage Object 
		# normally created by MW by a slightly modified 
		# processing flow.
		
		$ne = new EditPage( $eobj->mArticle );
		
		# let the new object follow the normal processing course
		$ne -> submit();
		
		# run our hook
		wfRunHooks( 'AfterEdit', array( &$ne ) );
		
		# Stop MW from performing normal processing on our
		# way to the standard work flow.
		# If this extension was loaded first against the 
		# 'AlternateEdit' hook, then other extensions on this hook
		# won't be called twice.
		return false;
	}
}
?>
