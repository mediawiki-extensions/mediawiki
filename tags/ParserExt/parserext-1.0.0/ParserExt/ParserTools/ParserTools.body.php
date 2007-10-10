<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserTools
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