<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class WidgetCodeStorage_Database
	extends WidgetCodeStorage {

	const VERSION = '@@package-version@@';
	const NAME    = 'securewidgets-csdb';
		
	static $nsName = "Widget";
	
	/**
	 * namespace prefix for the trans-cache
	 */
	static $prefixTransCache = 'sw-';
	
	/**
	 * Widget Name
	 */
	var $name = null;

	/**
	 * Constructor
	 */
	public function __construct( &$name ) {
	
		parent::__construct( $name );
	
	}

	public function get() {
	
		$dbName = $this->formatName( $this->name );
	
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
new WidgetCodeStorage_Database;
include "WidgetCodeStorage_Database.i18n.php";
