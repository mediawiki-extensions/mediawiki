<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class RepositoryFeed
	implements Iterator {

	const DEFAULT_FEED = "http://feeds.feedburner.com/jldupont/mw-widgets";
	
	var $feed = null;
	var $contents = null;
	var $liste = array();
	var $xml = null;

	public function __construct( $address = self::DEFAULT_FEED ) {
	
		$this->feed = $address;
	
	}
	/**
	 * 
	 */	
	public function fetch() {
	
		if (empty( $this->feed ))
			throw new Exception( __METHOD__.": feed URL is invalid" );
	
		$this->contents = Http::get( $this->feed );
		if ( $this->contents === false )
			return false;
			
		$result = $this->parse();
		if ( $result === false )
			return false;
			
		return $this->process();
	}

	protected function parse() {
		
		return ( ( $this->xml = simplexml_load_string( $this->contents ) ) !== false );
	}
	protected function process() {
	
		$items = $this->xml->channel[0]->item;
		foreach( $items as $item )
			$this->liste[] = new WidgetLocator( $item );

		return true;
	}
	/*********************************************************
	 * 				Iterator Interface
	 ********************************************************/	
	public function count() {

		return count( $this->liste );
	}
	public function current() {

		return current( $this->liste );
	}
	public function key() {

		return key( $this->liste );
	}
	public function next() {

		return next( $this->liste );
	}
	public function rewind() {
	
		return reset( $this->liste );
	}
	public function valid() {

		return ( key( $this->liste ) !== null );
	}
	
}//end class