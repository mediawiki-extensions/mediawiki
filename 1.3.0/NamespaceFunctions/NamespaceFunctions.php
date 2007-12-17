<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage NamespaceFunctions
 * @version 1.3.0
 * @Id $Id: NamespaceFunctions.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'        => 'NamespaceFunctions', 
	'version'     => '1.0.0',
	'author'      => 'Jean-Lou Dupont', 
	'description' => 'Provides namespace level functions.',
	'url' 		=> 'http://mediawiki.org/wiki/Extension:NamespaceFunctions',			
);

class NamespaceFunctions
{
	// constants.
	const thisName = 'NamespaceFunctions';
	const thisType = 'other';
		
	function __construct( ) {	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Functions which are meant to be accessed through 'ParserPhase2' functionality


	#public function mg_( &$parser, )
	// (($ #magic word | $))
	// 
	#{ }

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// Namespace related functions
	// Useful for building <select> <option> sections
	// e.g. (($#foreachx|$bwNamespaceFunctions|getRealNamespacesNames| ...

	public static function getNamespacesNames( $user = null, $right = 'read' )
	/*
		This method returns a list of valid namespaces according to the specified 'right' for 'user'
	*/
	{
		$l = null;
		
		if ( $user === null or $user == 0)
		{
			global $wgUser;
			$user = $wgUser;
		}
		global $wgCanonicalNamespaceNames;
			
		foreach( $wgCanonicalNamespaceNames as $id => $name )
			if ( $user->isAllowed( $right, $id ))
				$l[ $id ] = $name;
		
		// Namespace class does not return NS_MAIN by default....
		if ( $user->isAllowed( $right, NS_MAIN ))
			$l[ NS_MAIN ] = Namespace::getCanonicalName( NS_MAIN );
		
		ksort( $l );
		
		return $l;
	}//end

	public static function getNamespacesIDs( $user, $right )
	{
		$id = null;
		
		$l = self::getNamespacesNames( $user, $right );
		if (!empty($l))
			foreach( $l as $id => $name )
				$l2[] = $id;
				
		return $id;
	}

	public static function getRealNamespacesNames( $user, $right )
	// returns canonical names of 'real' namespaces i.e. ones with corresponding pages in the database
	// Basically excludes NS_SPECIAL and NS_MEDIA namespaces
	{
		$l = self::getNamespacesNames( $user, $right );
		if (!empty($l))
			foreach( $l as $id => $name )
				if ( $id < 0 )
					unset( $l[$id] );
		return $l;
	}


} // end class.
//</source>