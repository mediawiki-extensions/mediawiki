<?php
/**
 * @author Jean-Lou Dupont
 * @package PageTrail
 * @version 1.0.4
 * @Id $Id: PageTrail.php 710 2007-12-03 17:23:15Z jeanlou.dupont $
 */
//<source lang=php>
if (class_exists('StubManager'))
{
	StubManager::createStub2(	
		array(	'class' 		=> 'PageTrail', 
				'classfilename'	=> dirname(__FILE__).'/PageTrail.body.php',
				'hooks'			=> array(	'BeforePageDisplay',
											'SiteNoticeAfter',
											'UserToggles',
										),
				)
	);

	global $wgExtensionCredits;
	$wgExtensionCredits['other'][] = array( 
		'name'    		=> 'PageTrail',
		'version'		=> '1.0.4',
		'author'		=> 'Jean-Lou Dupont',
		'url'			=> 'http://www.mediawiki.org/wiki/Extension:PageTrail',	
		'description' 	=> "Provides a page trail (aka parser-cache friendly 'breadcrumbs')", 
	);
}
else
	echo 'Extension:PageTrail <b>requires</b> [[Extension:StubManager]]'."\n";

//</source>