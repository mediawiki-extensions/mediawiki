<?php
/**
 * @package Gliffy
 * @category Social
 * @author Jean-Lou Dupont
 * @version 1.0.0 
 * @Id $Id$
 */

if (!class_exists( 'ExtensionBaseClass' )) {

	echo "Missing dependency <a href='http://mediawiki.org/wiki/Extension:ExtensionManager'>ExtensionManager >= v2.0.1</a>";
	die(-1);
	
}

class MW_Gliffy 
	extends ExtensionBaseClass
{
	const VERSION = '1.0.0';
	const NAME    = 'gliffy';
	
	static $parameters = array(
	
		#URL parameters
		'did'			=> array( 'm' => true,  's' => false, 'l' => false, 'd' => null, 't' => 'number' )
	
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
		
		$this->setCreditDetails( array( 
			'name'        => $this->getName(), 
			'version'     => self::VERSION,
			'author'      => 'Jean-Lou Dupont', 
			'description' => 'Provides integration with online diagram publishing tool [http://www.gliffy.com Gliffy]',
			'url' 		=> 'http://mediawiki.org/wiki/Extension:Gliffy',			
			) );
		
		$this->setStatus( self::STATE_OK );
	}
	/**
	 * Parser Function #gliffy
	 */
	public function pfnc_gliffy( &$parser ) {
	
		$params = func_get_args();	
		
		$p = ExtensionHelperClass::processArgList( $params, true );
		
		$h = new ExtensionHelperClass( $p, self::$parameters );
	
		if ( $h->isError() )
			return $this->handleErrors( $h, 'gliffy' );
			
		$output = $this->format( $h );
			
		return array( $output, 'noparse' => true, 'isHTML' => true );		
	}
	/**
	 * Formats the required HTML
	 */
	protected function format( &$h ) {
	
		$liste = $h->getOutputList();
		
		// mandatory
		$did   = $liste[ 'did' ];

		$html = wfMsg( 'gliffy-html', $did );
		
		return $html;
	}
	
	
	
}//end class definition

// REQUIRED to bootstrap the extension setup process
new MW_Gliffy;

include 'Gliffy.i18n.php';
