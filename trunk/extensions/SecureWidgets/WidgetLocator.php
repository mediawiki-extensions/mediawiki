<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

class WidgetLocator {

	var $obj = null;

	const ANCHOR_PATTERN    = '/\<a(.*)\>/siU';
	const CLASS_PATTERN     = '/class\=[\'\"](.+)[\'\"]/siU';
	const HREF_PATTERN 		= '/href=[\'\"](.+)[\'\"]/siU';
	
	// example: http://mediawiki.googlecode.com/widget/gliffy-1.0.0.html
	const VERSION_PATTERN   = '/widgets\/(?:.+)\-(.*)\.html/siU';
	const NAME_PATTERN      = '/widgets\/(.*)-/siU';
	
	const CODE_CLASS        = 'widget-code';
	const HELP_CLASS		= 'widget-help';

	var $params = array();
	var $anchors = null;
	var $description = null;
	
	static $paramsList = array(
	
		'codelink' => true,
		'helplink' => true,
	
		// must appear *AFTER* codelink
		'version'  => true,
		'name'	   => true,
	);
	
	public function __construct( &$obj ) {
	
		$this->obj = $obj;
	
	}
	public function __get( $key ) {
	
		if ( !array_key_exists( $key, self::$paramsList ))
			throw new Exception( __METHOD__ .": invalid parameter ($key)");
		
		$this->extractParams();
	
		return $this->params[ $key ];
	}
	
	protected function extractParams() {
	
		// do this only once!
		if ( !empty( $this->params ) )
			return;

		$this->description = (string) $this->obj->description[0];
		$this->extractAnchors();
			
		foreach( self::$paramsList as $key => $extra ) {
		
			$method = "extract_$key";
			$this->$method();
		}

		unset( $this->obj );
	}
	protected function extractAnchors() {
	
		$result = preg_match_all( self::ANCHOR_PATTERN, $this->description, $matches );
		if ( $result !== false )
			$this->anchors = $matches[0];
	}
	protected function extract_codelink() {

		// find anchor with widget code class
		$index = $this->getElementByClass( self::CODE_CLASS );
		$href  = $this->getHrefByAnchorIndex( $index );
		
		$this->params['codelink'] = $href;
	}
	protected function extract_helplink() {

		// find anchor with widget help class
		$index = $this->getElementByClass( self::HELP_CLASS );
		$href  = $this->getHrefByAnchorIndex( $index );
		
		$this->params['helplink'] = $href;
	
	}
	protected function extract_version() {
		// version information is available through the codelink
		$cl = $this->params['codelink'];
		
		$r  = preg_match( self::VERSION_PATTERN, $cl, $match );
		if ( $r === false )
			$v = null;
		else
			$v = $match[1];
		$this->params['version'] = $v;
	}
	protected function extract_name() {
		// version information is available through the codelink
		$cl = $this->params['codelink'];
		
		$r  = preg_match( self::NAME_PATTERN, $cl, $match );
		if ( $r === false )
			$n = null;
		else
			$n = $match[1];
		$this->params['name'] = $n;
	}
	protected function getElementByClass( $classe ) {
	
		foreach( $this->anchors as $index => $anchor ) {
			$result = preg_match( self::CLASS_PATTERN, $anchor, $match );
			if ( $result !== false )
				if ( $classe == $match[1] )
					return $index;
		}
		return null;
	}
	protected function getHrefByAnchorIndex( $index ) {
	
		if (is_null( $index ))
			return null;
	
		$result = preg_match( self::HREF_PATTERN, $this->anchors[$index], $match );
		if ( $result !== false )
			return $match[1];
		return null;
	}
}//end class