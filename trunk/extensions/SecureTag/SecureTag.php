<?php
/**
 * @package SecureTag
 * @category ParserFunctions
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {
	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager</a>";
	die(-1);
}

class MW_SecureTag
	extends ExtensionBaseClass
{
	const VERSION = '@@package-version@@';
	const NAME = 'securetag';
	
	const ERROR_TAG_NOT_ALLOWED = 1;
	
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
			'description' => 'Provides a secure way of inserting tag sections',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:SecureTag',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	
	public function pfnc_tag( &$parser, $tag, $content, $attr = null ) {
	
		// first off, verify if the specified tag is allowed
		if ( !$this->isTagAllowed( $tag ))
			return $this->outputError( SELF::ERROR_TAG_NOT_ALLOWED, $tag );

		$output = <<<EOF
		<$tag $attr>$content</$tag>
EOF;
			
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}
	
	protected function isTagAllowed( &$tag ) {
		
		$msg = $this->getMessage( SELF::NAME . '/' . $tag );
		return ( is_null( $msg ) ) ? false : true;	
		
	}
	
	protected function outputError( $code, &$param = null ) {
		return wfMsg( SELF::NAME . '-error-' . $code, $param );
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
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_SecureTag;

// i18n Messages
include 'SecureTag.i18n.php';
