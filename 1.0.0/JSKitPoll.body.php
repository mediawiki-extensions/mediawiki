<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitPoll
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
class JSKitPoll
{
	const thisType = 'other';
	const thisName = 'JSKitPoll';

	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 */
	static $parameters = array(
		// not part of JS-Kit parameters

		// JS-Kit parameters:
		'style'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
	);
	/**
	 * {{#jskitpoll(: [optional parameters] }}
	 */
	public function mg_jskitpoll( &$parser )
	{
		$params = func_get_args();
		$liste = StubManager::processArgList( $params, true );		
		
		$output = $this->renderEntry( $liste );

		$output .= <<<EOT
	<script src="http://js-kit.com/polls.js"></script>
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
<div class="js-kit-poll" {$attrListe}></div>
EOT;
		return $output;
	}
} // end class
//</source>
