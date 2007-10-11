<?php
/**
 * @author Jean-Lou Dupont
 * @package DocProc
 * @version $Id$
 */
//<source lang=php>
class DocProc
{
	// constants.
	const thisName = 'DocProc';
	const thisType = 'other';
	
	static $allowedDocTags = array( 'code', 'pre' );
	static $defaultDocTag = 'code';
	
	function __construct( ) {}

	public function tag_docproc( &$text, &$params, &$parser )
	{
		$tag = @$params['tag'];
		
		// make sure the user is asking for a valid HTML tag for the documentation part.
		$docTag = (in_array($tag, self::$allowedDocTags)) ? ($tag) : (self::$defaultDocTag);		
		
		// parse the wikitext as per required as if the said text wasn't being automatically documented.
		$pt = $parser->recursiveTagParse( $text );
		
		return '<'.$docTag.'>'.htmlspecialchars($text).'</'.$docTag.'>'.$pt;
	}
} // end class

// </source>