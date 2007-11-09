<?php
/**
 * @author Jean-Lou Dupont
 * @package SimpleScript	
 * @version $Id$
 */
//<source lang=php>
class SimpleScript
{
	const thisType = 'other';
	const thisName = 'SimpleScript';
	
	public function __construct() {}
	
	/**
	 * {{#sscript:interwiki prefix|desired uri|message to show upon error on interwiki prefix}}
	 */
	public function mg_sscript( &$parser, $iw_prefix, $desired_uri, $error_msg = null )
	{
		$dummyTitle = new Title;
		
		$iw = $dummyTitle->getInterwikiLink( $iw_prefix );
		if (empty( $iw ))
			return $error_msg;
		
		// replace the $1 in the interwiki prefix with the desired URI
		$complete_uri = str_replace( '$1', $desired_uri, $iw );
		
		return '<script type= "text/javascript" src="'.$complete_uri.'"></script>'."\n";
	}
		
} // end class
//</source>
