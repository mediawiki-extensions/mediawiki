<?php
/**
 * @author Jean-Lou Dupont
 * @package ClassProcessor
 * @category Flow
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        => 'ClassProcessor', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Provides PHP class handling with autoloading',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:ClassProcessor',			
);

class MW_ClassProcessor
{
	/**
	 * @private
	 */
	static $_prefix = 'MW_';
	
	/**
	 * @private
	 */
	static $_prefixDatabase = "Code:";
	
	/**
	 * Additional autoloaders
	 * @private
	 */
	static $_eAutoloaders = array();
	
	/**
	 * Register the extension
	 */
	public static function init()
	{
		global $wgExtensionFunctions;
		$wgExtensionFunctions[] = array( __CLASS__, 'setup' );
	}	
	/**
	 * Sets up the autoloader
	 * @return 
	 */
	public static function setup()
	{
		spl_autoload_register( array( __CLASS__, 'autoloader' ) );
		
		// register the default MediaWiki autoloader if it isn't already
		spl_autoload_register( '__autoload' );		
	}
	public static function add( $autoloader )
	{
		self::$_eAutoloaders[] = $autoloader;
	}
	/**
	 * Autoloader
	 * 
	 * @return void
	 * @param $className string
	 */	
	public static function autoloader( $className )
	{
		// try the other registered autoloaders
		// great for adding/replacing standard functionality.
		if ( self::tryOthers( $className ) )
			return;
			
		// make sure we are asked to load a class
		// with the configured prefix name
		$len = strlen( self::$_prefix );
		$prefix = substr( $className, 0, $len );
		
		if ( $prefix !== self::$_prefix )
			return;
			
		// remove the prefix to get the name
		$name = substr( $className, $len );			

		//1st location: PEAR			
		if ( self::loadFromPear( $name ))
			return;

		//2nd location: MediaWiki extensions directory
		if ( self::loadFromExtensions( $name ))
			return;

		//3rd location: Database in namespace ''Code''
		self::loadFromDatabase( $name );
	}
	/**
	 * Iterates through the extra autoloaders
	 * 
	 * @return $result boolean
	 * @param $className string
	 */
	protected static function tryOthers( &$className )
	{
		foreach( self::$_eAutoloaders as $a )
		{
			$a->autoload( $className );
			if ( class_exists( $className ))
				return true;
		}
		
		return false;
	}
	/**
	 * Tries loading a class from the PEAR directory
	 * 
	 * @return $result boolean
	 * @param $className string
	 */	
	protected static function loadFromPear( &$className )
	{
		@include_once "MediaWiki/Classes/$className.php";
		return class_exists( self::$_prefix.$className );
	}	
	/**
	 * Tries loading a class from the ''extensions'' directory
	 * in the MediaWiki installation
	 * 
	 * @return $result boolean
	 * @param $className string
	 */	
	protected static function loadFromExtensions( &$className )
	{
		global $IP;
		@include_once "$IP/extensions/Classes/$className.php";
		return class_exists( self::$_prefix.$className );
	}	
	/**
	 * Tries loading the required class from the database
	 * 
	 * @return $result boolean
	 * @param $className string
	 */
	protected static function loadFromDatabase( &$className )
	{
		$page = self::$_prefixDatabase.$className;
		
		$contents = self::getPage( $page );
		
		$code = null;
		if ( !empty( $contents ))
			$code = self::extractCodeFromContents( $contents );
			
		if ( !empty( $code ))			
			self::prepareCode( $code );
			
		return class_exists( self::$_prefix.$className );
	}
	
	/**
	 * Fetches a page's ''raw'' content from the database
	 * The page must be ''edit protected'' for security reasons
	 * 
	 * @return $content string
	 * @param $page string
	 */
	protected static function getPage( $page )
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
	protected static function extractCodeFromContents( &$contents )	
	{
		// get rid of PHP opening tag
		$contents = str_replace( '<?php', '', $contents );

		$result = preg_match( "/<source(?:.*)>(.*)<\/source>/siU" , $contents, $match );
		if ( $result === 1 )
			return $match[1];
		
		return $contents;
	}
	/**
	 * Instantiate the class
	 * 
	 * @return $result mixed
	 * @param $code string
	 * @todo Check if the code makes it in the bytecode cache
	 */	
	protected static function prepareCode( &$code )
	{
		return eval( $code );		
	}
	
} //end class

MW_ClassProcessor::init();

//</source>