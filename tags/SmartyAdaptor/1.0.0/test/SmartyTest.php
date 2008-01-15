<?php
/*
 * SmartyTest.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *
 */
class SmartyTest extends Smarty
{
	public function __construct()
	{
		$this->assign('testvariable', 'This is a test variable');	
	}
}
 
 ?>
 