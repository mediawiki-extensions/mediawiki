<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage ParserTools
 * @version 1.3.1
 * @Id $Id: ParserTools.body.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
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