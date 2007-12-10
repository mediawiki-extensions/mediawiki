<?php
/**
 * @author Jean-Lou Dupont
 * @package EmbedObject
 * @version 1.0.0
 * @Id $Id$
*/
//<source lang=php>
class EmbedObject
{
	const thisType = 'other';
	const thisName = 'EmbedObject';

	/**
	 * {{#embed: mime-type|local-or-interwiki-link[|param1=value1 ...]}}
	 */
	public function mg_embed(	&$parser,	
								$type,			// MIME-type
								$iwl_link		// interwiki prefix
								/* extra parameters fetched from the call stack */)
	{
		// sanitize the input
		$sanitized_type = htmlspecialchars( $type );
		$sanitized_link = htmlspecialchars( $iwl_link );
		
		// deal with the variable parameters list.
		$params = func_get_args();
		array_shift( $params ); // get rid of $parser
		array_shift( $params ); // get rid of $type		
		array_shift( $params ); // get rid of $iwl_link		

		// sanitizes & formats the params list as a string.
		$liste = $this->formatParamsList( $params );
		
		// check the validation of the interwiki prefix
		$msg_id = 'importbadinterwiki';
		$link = $this->getLink( $sanitized_link );
		if ($link === false)
			return wfMsg( $msg_id ); // use an already translated message.

		$completeElement = '<embed type="'.$type.'" src="'.$link.'" '.$liste.' />';
		
		// prepare for the call to [[Extension:ParserFunctionsHelper]]
		// public function hParserFunctionsHelperSet( $key, &$value, &$index, &$anchor )		
		$anchor = null;
		$index = null;
		wfRunHooks( 'ParserFunctionsHelperSet',
					array( 'embed', &$completeElement, &$index, &$anchor ) );
		
		if ( $anchor === null)
			return '[http://www.mediawiki.org/wiki/Extension:ParserFunctionsHelper Extension:ParserFunctionHelper] missing.';
		
		return $anchor;		
	}
	/**
	 * Returns a fully qualified URL link from an interwiki link
	 */
	public function getLink( &$iwl )
	{
		$ititle = Title::newFromText( $iwl );

		// this really shouldn't happen... not much we can do here.		
		if (!is_object($ititle)) 
			return false;

		return $ititle->getFullUrl();
	}	 
	/**
	 * Each parameter comes in the form 'key=value'.
	 * We need to espace the double quotes in order
	 * to reduce the potential for XSS attacks etc.
	 */
	public function formatParamsList( &$params )
	{
		if (empty( $params ))
			return null;
		
		$result = '';
		foreach( $params as $index => &$e )
		{
			$delimiter_pos = strpos( $e, '=' );
			$bit0 = substr( $e, 0,  $delimiter_pos );
			$bit1 = substr( $e, $delimiter_pos+1 );
			
			$key = htmlspecialchars( $bit0 );
			$value = $this->espaceDoubleQuotes( $bit1 );

			$result .= $key.'="'.$value.'" ';
		}
		return $result;
	}	
	/**
	 * only espaces
	 */
	public function espaceDoubleQuotes( $input )
	{
		return str_replace('"', '&quot;', $input );
	}
	
} // end class

//</source>
