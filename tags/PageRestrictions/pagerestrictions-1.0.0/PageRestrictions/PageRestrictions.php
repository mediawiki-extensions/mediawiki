<?php
/**
 * @author Jean-Lou Dupont
 * @package PageRestrictions
 * @version $Id$
*/
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'		=> 'PageRestrictions',
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => "Adds page level restrictions definitions & enforcement.",
);

StubManager::createStub(	'PageRestrictions', 
							dirname(__FILE__).'/PageRestrictions.body.php',
							null,
							array('ArticleViewHeader'),					// hooks
							false, 					// no need for logging support
							null,					// tags
							null,
							null
						 );
class PageRestrictionsSetup
{
	static $rList  = array(	
						'read',			// This right is enforced by this extension
						'raw',			// This right is enforced by [[Extension:RawRight]]
						'viewsource',	// This right is enforced by [[Extension:ViewsourceRight]]
						);
	
	public static function setup()
	{
		global $wgRestrictionTypes;
		
		foreach( self::$rList as $index => $rest )
			$wgRestrictionTypes[] = $rest;
	}
	
} // end class

PageRestrictionsSetup::setup();

//</source>
