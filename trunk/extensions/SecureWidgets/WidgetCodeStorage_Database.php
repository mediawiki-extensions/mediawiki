<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class MW_WidgetCodeStorage_Database
	extends MW_WidgetCodeStorage {

	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets-csdb';
		
	static $nsName = "Widget";
	
	static $instance = null;
	
	/**
	 * Constructor
	 */
	public function __construct( ) {
	
		if ( self::$instance !== null )
			throw new Exception( __CLASS__.": singleton required" );
		self::$instance = $this;
	
		parent::__construct( );
	
	}
	public function setup() {
	}
	public static function gs() {
		return self::$instance;
	}
	public function getCode() {
	
		// verify if the namespace exists
		if ( !$this->nsExists( self::$nsName ) ) {

			$entry = array( 'id' => self::NAME . '-not-ns',
							'p'	 => array( self::$nsName ) );
			$this->pushError( $entry );
			return null;
		}
		
		// we can now try to fetch the code from the database
		$id = null;
		$title = null;
		$dbName = $this->formatName( $this->name );
		$article = $this->buildArticle( $this->name, $title );
		$code = $this->fetchPageFromParserCache( $article, $id );
		if ( $code !== false )
			return $code;
			
		$code = $this->fetchPageFromDatabase( $title );
		if ( $code !== false )
			return $code;
		
		$entry = array( 'id' => self::NAME . '-not-db' );
		$this->pushError( $entry );
		return null;
	}

	/**
	 * Formats the widget name for access through the database
	 * 
	 * @param $name string
	 * @return $dbname string
	 */
	protected function formatName( &$name ) {

		return self::$nsName . $name ;
	
	}
	
	/**
	 * Verifies if the given namespace exists
	 * 
	 * @param $nsname string
	 * @return $result boolean
	 */
	protected function nsExists( &$nsname ) {
	
		$id = Namespace::getCanonicalIndex( $nsname );
		return ( is_null( $id ) ? false:true );
	}
	
	/**
	 * Builds a valid article object
	 * 
	 * @return $article Object
	 * @param $prefix string
	 * @param $name string
	 */
	protected function buildArticle( &$name, &$title ) {
	
		// build a title object
		$title = Title::newFromText( $name );
		if (!is_object( $title ))
			return false;
			
		return new Article( $title );
	}
	/**
	 * Fetches a given article from the parser cache
	 * 
	 * @return $result string
	 * @param $article Object
	 */
	protected function fetchPageFromParserCache( &$article, &$id ) {
	
		global $wgUser;
				
		$parserCache =& ParserCache::singleton();
		$po = $parserCache->get( $article, $wgUser );
		if ( is_object( $po ) ) {
			$id = $po->getCacheTime();
			return $po->getText();
		}
			
		return false;
	}
	/**
	 * Fetches a page directly from the database
	 * 
	 * @return $result string
	 * @param $prefix string
	 * @param $name string
	 */
	protected function fetchPageFromDatabase( &$title ) {
	
		$contents = false;
		$rev = Revision::newFromTitle( $title );

		if( is_object( $rev ) )	{
			$id = $rev->getId();
		    $contents = $rev->getText();		
		}

		return $contents;
	}
	
}
new MW_WidgetCodeStorage_Database;
include "WidgetCodeStorage_Database.i18n.php";
