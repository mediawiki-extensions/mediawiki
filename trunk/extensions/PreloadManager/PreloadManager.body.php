<?php
/**
 * @author Jean-Lou Dupont
 * @package PreloadManager
 * @version $Id$
 */
//<source lang=php>
class PreloadManager
{
	const thisType = 'other';
	const thisName = 'PreloadManager';
	
	/**
	 */
	public function __construct() {}
	/**
	 */
	//wfRunHooks( 'EditFormPreloadText', array( &$this->textbox1, &$this->mTitle ) );
	public function hEditFormPreloadText( &$text, &$title )
	{
		$text = $this->loadTemplate( $title );
		
		return true;
	}
	/**
	 */
	protected function loadTemplate( &$title )
	{
		$full_name = $title->getPrefixedText();
		
		$match_index = $this->findMatch( $full_name );
		if ($match_index === null)
			return null;
		
		$filename = PreloadRegistry::$map[ $match_index ]['filename'];
		
		return @file_get_contents( $filename );
	}
	/**
	 */
	protected function findMatch( &$name )
	{
		$match = null;
		if (empty( PreloadRegistry::$map ))
			return null;
		foreach( PreloadRegistry::$map as $index =>&$e )
		{
			$r = preg_match( $e['pattern'], $name );
			if ($r===1)
			{
				$match = $index;
				break;	
			}
		}
		return $match;
	}	
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

/*	 
	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits;

		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1;
				
		return true; // continue hook-chain.
	}
*/

} // end class
//</source>