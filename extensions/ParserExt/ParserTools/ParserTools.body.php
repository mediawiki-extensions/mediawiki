<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ParserTools
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>*/
class ParserTools
{
	// constants.
	const thisName = 'ParserTools';
	const thisType = 'other';
	  
	function __construct(  ) {	}

	public function tag_noparsercaching( &$text, &$params, &$parser )
	{ $parser->disableCache(); }

} // end class
//</source>