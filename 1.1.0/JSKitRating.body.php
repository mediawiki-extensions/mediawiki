<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitRating
 * @version 1.1.0
 * @Id $Id: JSKitRating.body.php 850 2008-01-06 04:46:34Z jeanlou.dupont $
 */
//<source lang=php>
class JSKitRating
{
	const thisType = 'other';
	const thisName = 'JSKitRating';

	// for i18n messages
	static $msg = array();
	
	/*
	 * m: mandatory parameter
	 * s: sanitization required
	 * l: which parameters to pick from list
	 * d: default value
	 */
	static $parameters = array(
		'imageurl'	=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
		'imagesize'	=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
		'path'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
		'starColor'	=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
		'userColor'	=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),				
		'view'		=>array( 'm' => false, 's' => true, 'l' => true, 'd' => null ),		
	);
	/**
	 * {{#jskitrating: [optional parameters] }}
	 */
	public function mg_jskitrating( &$parser )
	{
		$params = func_get_args();
		
		$liste = StubManager::processArgList( $params, true );
	
		// all parameters are optional	
		$sliste= ExtHelper::doListSanitization( $liste, self::$parameters );
		$attrListe = null;
		if (is_array( $sliste ))
		{		
			$r = ExtHelper::doSanitization( $sliste, self::$parameters );
			$attrListe = ExtHelper::buildList( $liste, self::$parameters );
		}
		
		global $wgTitle;
		
		$title = $wgTitle->getPrefixedDBkey();
		
		$output = <<<EOT
<div class="js-kit-rating" title="$title" permalink="$title" {$attrListe}></div>
<script src="http://js-kit.com/ratings.js"></script>
EOT;

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

} // end class
//</source>
