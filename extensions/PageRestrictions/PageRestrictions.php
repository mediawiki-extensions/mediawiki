<?php
/**
 * @author Jean-Lou Dupont
 * @package PageRestrictions
 * @version @@package-version@@
 * @Id $Id$
*/
//<source lang=php>
if (class_exists('StubManager'))
{
	$wgExtensionCredits['other'][] = array( 
		'name'		=> 'PageRestrictions',
		'version'     => '@@package-version@@',
		'author'      => 'Jean-Lou Dupont', 
		'description' => "Adds page level restrictions definitions & enforcement.",
		'url'		=> 'http://mediawiki.org/wiki/Extension:PageRestrictions'
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
		static $msg = array();
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
				
			global $wgExtensionFunctions;
			$wgExtensionFunctions[] = create_function('',"return PageRestrictionsSetup::loadMessages();");
		}
		public static function loadMessages()
		{
			global $wgMessageCache;
			foreach( self::$msg as $key => $value )
				$wgMessageCache->addMessages( self::$msg[$key], $key );		
		}
		
	} // end class
	require('PageRestrictions.i18n.php');	
	PageRestrictionsSetup::setup();
}
else
	echo 'Extension:PageRestrictions requires Extension:StubManager';
//</source>
