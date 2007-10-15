<?php
/**
 * @author Jean-Lou Dupont
 * @package PreloadManager
 * @version $Id$
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        => 'PreloadManager', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Manages page text preloading",
	'url'		=> 'http://mediawiki.org/wiki/Extension:PreloadManager',
);
StubManager::createStub2(	array(	'class' 		=> 'PreloadManager', 
									'classfilename'	=> dirname(__FILE__).'/PreloadManager.body.php',
									'hooks'			=> array(	'EditFormPreloadText',
																//'SpecialVersionExtensionTypes',
															),
								)
						);
class PreloadRegistry
{
	static $map = array();

	/**
	 */
	public function set( &$entry )
	{
		self::$map[] = $entry;
	}
	public function get()
	{
		return self::$map;	
	}
}						
//</source>