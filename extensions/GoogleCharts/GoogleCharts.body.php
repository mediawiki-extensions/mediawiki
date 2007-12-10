<?php
/**
 * @author Jean-Lou Dupont
 * @package GoogleCharts
 * @version @@package-version@@
 * @Id $Id$
*/
//<source lang=php>
class GoogleCharts
{
	const thisType = 'other';
	const thisName = 'GoogleCharts';

	static $url = 'http://chart.apis.google.com/chart?';
	
	var $charts = array();
	
	/**
	 * Replaces all the gcharts information opaque to the parser.
	 */
	public function hParserAfterTidy( &$parser, &$text )
	{
		$this->findAnchorsAndReplace( $text );
		return true;
	}
	/**
	 * Decodes it.
	 */
	protected function findAnchorsAndReplace( &$text )
	{
		if (empty( $this->charts ))
			return null;

		foreach( $this->charts as $index => &$e )
			$text = str_replace( '__gcharts__'.$index.'__', $e, $text );

		return true;
	}
	/**
	 * {{#gcharts_pipe: param1, param2, ...}}
	 * Returns a string formatted using the pipe '|' character
	 * as delimiter. This is required since the pipe character
	 * is used in MediaWiki's parser functions.
	 */
	public function mg_gcharts_pipe( &$parser, $params )
	{
		if (empty( $params ))
			return null;
		$bits = explode( ',', $params );
		return implode( '|', $bits );
	}	
	/**
	 * {{#gcharts: param1&param2&...[|alternate text]}}
	 */
	public function mg_gcharts( &$parser, $params, $altText = 'chart' )
	{
		// sanitize the input
		$sanitized_params = htmlspecialchars( $params );
		
		// format the img tag.
		$element = '<img src="'.self::$url.$sanitized_params.'" alt="'.$altText.'" />';
		
		// Hides the information from the parser or else it gets mangled.
		$this->charts[] = $element;
		
		// just give an harmless string to the parser.
		// We don't want to let an encoded string representing the code around 
		// or else this would introduce a security hole.
		return '__gcharts__'.( count( $this->charts ) -1 ).'__';
	}
	/**
	 * Parser function for handling the 'simple encoding' procedure
	 * {{#gcharts_data: value1, value2, ... | maxValue }}	 
	 */
	public function mg_gcharts_senc( &$parser, $params, $maxValue )
	{
		$bits = explode( "," , $params );
		if (empty( $bits ))
			return null;
		return 'chd=s:'.$this->simpleEncoding( $bits, $maxValue );
	}
	/**
	 * Simple Encoding function
	 */
	protected function simpleEncoding( &$dataArray, $maxValue )
	{
		static $simpleEncoding = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		static $len = 62;
		
		$result = '';
		
		if (empty( $dataArray ))
			return null;
			
		foreach( $dataArray as $index => &$e )
		{
			if (is_numeric( $e ) && ( $e >=0 ))
			{
				$pos = round( (self::$len-1)*($e / $maxValue));
				$result .= self::$simpleEncoding[$pos];
			}
			else
				$result .= '_';
				
		}
		return $result;
	}	
} // end class

//</source>
