<?php
$wgExtensionCredits[FormProc::thisType][] = array( 
	'name'        => FormProc::thisName, 
	'version'     => StubManager::getRevisionId( '$Id$' ),
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Handles "action=formsubmit" post requests through page based PHP code',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:FormProc',			
);

class FormProc
{
	// constants.
	const thisName = 'FormProc';
	const thisType = 'other';
		  
	function __construct( ) {}

	public function hUnknownAction( $action, &$article )
	{
		// check if request 'action=formsubmit'
		if ($action != 'formsubmit') return true; // continue hook-chain.

		$article->loadContent();

		// follow redirects
		if ( $article->mIsRedirect == true )
		{
			$title = Title::newFromRedirect( $article->getContent() );
			$article = new Article( $title );
			$article->loadContent();
		}
		// Extract the code
		// Use our runphpClass helper
		$runphp = new runphpClass;
		$runphp->initFromContent( $article->getContent() );	

		// Execute Code
		$code = $runphp->getCode( true ); 

		if (!empty($code))
			$callback = eval( $code );  // we might implement functionality around a callback method in the future

		// Was there an expected class defined?
		$name = $article->mTitle->getDBkey();

		// the page name might actually be a sub-page; extract the basename without the full path.
		$pn   = explode( '/', $name );
		if ( !empty( $pn ))
		{
			$rn = array_reverse( $pn );
			$name = $rn[0];
		}
		$name .= 'Class';

		if ( class_exists( $name ))
		{
			$class = new $name();
			if ( is_object( $class))
				if (method_exists( $class, 'submit' ))
					$class->submit();
		}	

		// ... then it was a page built from ground up; nothing more to do here.
		return false;
	}

} // END CLASS DEFINITION

//</source>