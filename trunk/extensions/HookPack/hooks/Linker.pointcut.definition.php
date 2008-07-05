<?php
/**
 * Linker class pointcuts
 * 
 * @author Jean-Lou Dupont
 * @see http://php-aop.googlecode.com/
 * @see http://www.ohloh.net/projects/php-aop
 */

class Linker_pointcuts

	extends aop_pointcut_definition {

	/**
	 * Pointcut definition
	 */
	public function cut_getInternalLinkAttributesObj() {

		return array(	'cp' 	=> 'Linker', 
						'mp'	=> 'getInternalLinkAttributesObj', 
						'am'	=> array(	'before' => 'before_show' )
				);
	
	}
	
	/**
	 * Advice definition 'before'
	 */
	public function before_show() {
		echo "aop( ".__METHOD__." class($class) )\n";
	}
	
}
