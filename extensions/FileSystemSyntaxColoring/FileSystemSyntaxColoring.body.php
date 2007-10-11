<?php
/**
 * @author Jean-Lou Dupont
 * @package FileSystemSyntaxColoring
 * @version $Id$
 */
//<source lang=php>
class FileSystemSyntaxColoring
{
	const thisName = 'FileSystem Syntax Coloring';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
		
	var $text;
	
	static $patterns = array(
	'/\<\?php/siU'							=> '',
	'/\/\*\<\!\-\-\<wikitext\>\-\-\>/siU'	=> '',
	'/\/*\<\!\-\-\<(.?)wikitext\>\-\->/siU'	=> '',
	'/\/\/\<(.?)source\>/siU' 				=> '<$1source>',
	'/\<source(.*)\>\*\//siU'				=> '<source $1>',
	'/\<\!\-\-\@\@/siU' 					=> '',
	'/\@\@\-\-\>/siU' 						=> ''
	);
	
	public function __construct() 
	{
		$this->text  = null;
	}
	
	public function hArticleAfterFetchContent( &$article, &$content )
	{
		// we are only interested in page views.
		global $action;
		if ($action != 'view') return true;

		// first round of checks
		if (!$this->isFileSystem( $article )) return true; // continue hook-chain
		
		// grab the content for later inspection.
		$this->text = $article->mContent;
		
		return true;
	}

	public function hParserBeforeStrip( &$parser, &$text, &$mStripState )
	// wfRunHooks( 'ParserBeforeStrip', array( &$this, &$text, &$this->mStripState ) );
	{
		// first round of checks
		if (!$this->isFileSystem( $parser )) return true; // continue hook-chain
		
		// since the parser is called multiple times, 
		// we need to make sure we are dealing the with article per-se
		if (strcmp( $this->text, $text)!=0 ) return true;
		
		// Check for a <wikitext> section
		$this->cleanCode( $text );
		
		return true;		
	}
	
	private function isFileSystem( &$obj )
	{
		// is the namespace defined at all??
		if (!defined('NS_FILESYSTEM')) return false;
		
		$ns = $obj->mTitle->getNamespace();

		// is the current article in the right namespace??		
		return (NS_FILESYSTEM == $ns)? true:false;
	}

	public function cleanCode( &$text )
	{
		foreach( self::$patterns as $pattern => $replacement )	
		{
			$r = preg_match_all( $pattern, $text, $m );
			if ( ( $r === false ) || ( $r ===0 ) )
				continue;
			
			foreach( $m[0] as $index => $c_match )
			{
				if ( isset( $m[1][$index] ) )				
					$rep = str_replace('$1', $m[1][$index], $replacement );
				else
					$rep = $replacement;
					
				$clean_text = str_replace( $c_match, $rep, $text );
				$text = $clean_text;
			}
		}
	}
	
} // end class definition.

//</source>