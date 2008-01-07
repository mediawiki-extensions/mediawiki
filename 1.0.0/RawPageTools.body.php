<?php
/**
 * @author Jean-Lou Dupont
 * @package RawPageTools
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
class RawPageTools
{
	const thisType = 'other';
	const thisName = 'RawPageTools';
	
	static $map = array( 
						'js' 	=> 'text/javascript',
						'css'	=> 'text/css',
						);

	/**
	 * Main Hook
	 */	
	public function hRawPageViewBeforeOutput( &$rp, &$text )
	{
		// make sure it is a document type we support.
		$tag  = $this->getRequestedTag( $rp );
		
		if (empty( $tag ))
			return true;
		
		// try to extract a tagged section.
		// If we don't succeed, then don't touch anything.
		$section = $this->getSection( $tag, $text );
		if ( $section !== false )
			$text = $section;
		
		return true;
	}

	public function getSection( &$tag, &$content )
	{
		if (empty( $tag ))
			return false;
			
		$pattern = '/'.$tag.'(?:.*)\>(.*)(?:\<.?'.$tag.'>)/siU';
		
	 	$result = preg_match( $pattern, $content,  $section );
		if ( $result >0 )
			return $section[1];
			
		return false;			
	}
	private function getRequestedTag( &$rp )
	{
		// examines 'ctype' request parameter
		return array_search( $rp->mContentType, self::$map );			
	}
}
//</source>