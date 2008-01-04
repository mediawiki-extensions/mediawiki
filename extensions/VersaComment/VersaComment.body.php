<?php
/**
 * @author Jean-Lou Dupont
 * @package VersaComment
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class VersaComment
{
	/**
	 * Removes <--{{  and  }}-->  terms
	 */
	public function hParserBeforeStrip( &$parser, &$text, &$mStripState )
	{
		$text = str_replace( '<--{{', '', $text );
		$text = str_replace( '}}-->', '', $text );		
		return true;
	}
}
//</source>
