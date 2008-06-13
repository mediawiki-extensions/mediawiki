<?php
/**
 * @package QuickEditProtect
 * @category AJAX
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {

	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager >= v2.0.1</a>";
	die(-1);
	
}
 
/**
 * Class definition
 */
class MW_QuickEditProtect
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME    = 'quickeditprotect';

	/**
	 * Return Codes
	 */
	const FAILURE                 = -1;
	const SUCCESS                 = 0;
	const TITLE_INVALID           = 1;
	const USER_INSUFFICIENT_RIGHT = 2;
	
	static $codeMap = array(
		self::FAILURE 					=> 'failure',
		self::SUCCESS 					=> 'success',
		self::TITLE_INVALID 			=> 'invalid title',
		self::USER_INSUFFICIENT_RIGHT	=> 'user does not have sufficient right'
	);
	
	static $codeHttpMap = array(
		self::FAILURE 					=> 500,
		self::SUCCESS 					=> 200,
		self::TITLE_INVALID 			=> 406,
		self::USER_INSUFFICIENT_RIGHT	=> 403,
	);
	
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
		
		global $wgAjaxExportList;
		
		// register our AJAX handlers
		$wgAjaxExportList[] = 'MW_QuickEditProtect::toggle';
	
		$this->setCreditDetails( array( 
			'name'        => $this->getName(), 
			'version'     => self::VERSION,
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Provides AJAX protect/unprotect for the edit right. ',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:QuickEditProtect',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}

	/**
	 * HOOK SpecialVersionExtensionTypes
	 * 
	 * Adds a nice help message to Special:Version page
	 *  when a sysop visits it
	 */	
	public function hook_SpecialVersionExtensionTypes( &$sp, &$extensionTypes ){

		global $wgUser;
		
		// only show this extra-info to sysop...
		$groups = $wgUser->getGroups();
		
		if ( !in_array( 'sysop', $groups ) )
			return true;
	
		$helpMsg = wfMsg( self::NAME . '-help' );
	
		$this->addToCreditDescription( $helpMsg );
				
		// required for all hooks
		return true; #continue hook-chain
	}
	
	/**
	 * HOOK SkinTemplateTabs
	 */
	public function hook_SkinTemplateTabs( &$st , &$content_actions ) {
	
		$status = $this->getEditProtectionStatus( $st->mTitle );
		
		// fragment identifier for the class attribute
		//  corresponds to the action that can be ordered
		$elementClassFragment = ($status) ? 'unprotect':'protect';
	
		global $wgUser;

		// ENFORCE PROTECT RIGHT
		if ($wgUser->isAllowed( 'protect') ) {
		
			$content_actions[ self::NAME ] = array(
			
				'text' => wfMsg( self::NAME . '-' . $elementClassFragment ),
				'href' => $st->mTitle->getLocalUrl( 'action=ajax&rs=' . __CLASS__.'::toggle' )
			);
		}

		return true;
	}
	/**
	 * Returns the status of the 'edit' protection.
	 * 
	 * @param $title object 
	 * @return boolean
	 */
	protected function getEditProtectionStatus( &$title ) {
	
		return $title->isProtected( 'edit' );
	}
	
	/*======================================================================
	 * AJAX HANDLER
	 ======================================================================*/
	
	public static function toggle( $pagename ) {
	
		global $wgUser;

		$title = Title::newFromDBkey($pagename);
		if ( !$title )
			return self::returnMsg( self::TITLE_INVALID, $protectState );
		
		$aid = $title->getArticleID();

		// title must exist!
		if ( $aid == 0 ) {
			return self::returnMsg( self::TITLE_INVALID, $protectState );
		} 
		
		// user must have the required clearance
		if ( !$wgUser->isAllowed( 'protect') )
			return self::returnMsg( self::USER_INSUFFICIENT_RIGHT, $protectState );

		$protectState = $title->isProtected('edit');
			
		// format the restriction
		//  whilst inverting the state...
		$group = ( $protectState ) ? '' : 'sysop';
		$restriction = array( 'edit' => $group );
		
		// update!
		$article = new Article( $title );
		
		$code = $article->updateRestrictions( $restriction );
		$msg  = ( $code ) ? self::SUCCESS : self::FAILURE;
		
		// update protection status
		$protectState = $code ? !$protectState:$protectState;
		
		return self::returnMsg( $msg, $protectState );
	}
	/**
	 * Returns the JSON message
	 */
	protected static function returnMsg( $code, $state ) {
	
		$msgId = self::NAME .'-'. ( $state ? 'unprotect':'protect' );
	
		$response = array(
			'code' 	=> $code,
			'msg'  	=> self::translateCode( $code ),
			'state'	=> $state,
			'text'  => wfMsg( $msgId )
		);

		$result = self::SimpleJsonEncode( $response );
				
		$ajaxResponse =  new AjaxResponse( $result );
		$ajaxResponse->setContentType( 'application/json' );
		$ajaxResponse->setResponseCode( self::$codeHttpMap[$code] );
		
		return $ajaxResponse;
	}
	
	protected static function translateCode( $code ) {
		return self::$codeMap[ $code ];
	}
	/**
	 * No need to compile PHP with JSON
	 * e.g. {"code":0,"msg":"success", "state":"1"}
	 */
	protected static function SimpleJsonEncode( &$response ) {
		$code = $response['code'];
		$msg  = $response['msg'];
		$state= $response['state'];
		$text = $response['text'];
		return <<<EOF
{"code":"$code","msg":"$msg","state":"$state","text":"$text"}
EOF;
	}
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_QuickEditProtect;
include 'QuickEditProtect.i18n.php';

