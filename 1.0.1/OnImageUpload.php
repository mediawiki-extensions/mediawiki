<?php
/**
 * @package OnImageUpload
 * @category Enhancements
 * @author Jean-Lou Dupont
 * @version 1.0.1 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {

	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager >= v2.0.1</a>";
	die(-1);
	
}

/**
 * Class definition
 */
class MW_OnImageUpload 
	extends ExtensionBaseClass
{
	const VERSION = '1.0.1';
	const NAME    = 'onimageupload';
	const TEXT    = 'onimageupload-text';
	
	/**
	 * If a constructor is required, then the
	 * parent class must be called first. 
	 */
	public function __construct(){
	
		parent::__construct();
	}
	/**
	 * Credits
	 */
	protected function setup(){
		
	
		$this->setCreditDetails( array( 
			'name'        => $this->getName(), 
			'version'     => self::VERSION,
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Provides automated header and footer text to Image description pages on upload. ',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:OnImageUpload',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	/**
	 *  Help message in [[Special:Version]] page
	 */	
	public function hook_SpecialVersionExtensionTypes( &$sp, &$extensionTypes ){

		global $wgUser;
		$groups = $wgUser->getGroups();
		
		if ( !in_array( 'sysop', $groups ) )
			return true;
			
		$this->addToCreditDescription( 'Image namespace [[Mediawiki:' . self::TEXT . '|preload text]] page.' );
				
		// required for all hooks
		return true; #continue hook-chain
	}
	/**
	 * HOOK ArticleSave
	 */
	public function hook_ArticleSave( &$article, &$user, &$text, &$summary, $minor,	$na1, $na2, &$flags) {

		$title = $article->mTitle;
		$ns    = $title->getNamespace();	
		$id = $article->getID();
		
		// make sure we are dealing with an article in the Image namespace
		if ( NS_IMAGE != $ns )
			return true;	
	
		// make sure it is a new page too
		if ( 0 != $id )
			return true;
			
		// we are good to go!
		$preloadText = $this->getMsg( self::TEXT );
		
		$text = $header . $preloadText. $footer;
		
		return true;
	}

	/**
	 * Gets a message from the NS_MEDIAWIKI namespace
	 */
	protected function getMsg( $msgId )
	{
		$msgText = wfMsgExt( $msgId );
		
		if ( wfEmptyMsg( $msgId, $msgText ))
			return null;
			
		return $msgText;			
	}	 
	
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_OnImageUpload;
