<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitNavigator
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class JSKitNavigator
{
	const thisType = 'other';
	const thisName = 'JSKitNavigator';

	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 */
	static $parameters = array(
		// JS-Kit parameters:
		'title'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),
		'count'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),
		'skin'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),
		'style'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
	);
	/**
	 * {{#jskitnavigator: [optional parameters] }}
	 */
	public function mg_jskitnavigator( &$parser )
	{
		$params = func_get_args();
		$liste = StubManager::processArgList( $params, true );		
		
		$output = $this->renderEntry( $liste );

		$output .= <<<EOT
	<script src="http://js-kit.com/top.js"></script>
EOT;

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	/**
	 * Returns 1 fully rendered DIV section
	 */
	protected function renderEntry( &$liste )
	{
		// all parameters are optional	
		$sliste= ExtHelper::doListSanitization( $liste, self::$parameters );
		$attrListe = null;
		if (is_array( $sliste ))
		{		
			$r = ExtHelper::doSanitization( $sliste, self::$parameters );
			$attrListe = ExtHelper::buildList( $liste, self::$parameters );
		}

		$output = <<<EOT
<div class="js-kit-top" {$attrListe}></div>
EOT;
		return $output;
	}
} // end class
//</source>
