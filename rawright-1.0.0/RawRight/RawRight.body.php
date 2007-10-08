<?php
/**
 * @author Jean-Lou Dupont
 * @package RawRight
 */
//<source lang=php>*/
class RawRight
{
	const thisName = 'RawRight';
	const thisType = 'other';  // must use this type in order to display useful info in Special:Version
	
	// Our class defines magic words: tell it to our helper class.
	public function __construct() {}

	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		// first check if the proper rights management class is in place.
		if (class_exists('HNP'))
			$hresult = '<b>HNP extension operational</b>';
		else
			$hresult = '<b>HNP extension <i>not</i> operational</b>';

		// check directly in the source if the hook is present 
		$rawpage = @file_get_contents('includes/RawPage.php');
		
		if (!empty($rawpage))
			$r = preg_match('/RawPageViewBeforeOutput/si',$rawpage);
		
		if ( $r==1 )
			$rresult = '<b>RawPageViewBeforeOutput hook operational</b>';
		else
			$rresult = '<b>RawPageViewBeforeOutput hook <i>not</i> operational</b>';
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))
				if ($el['name']==self::thisName)
					$el['description'].=$hresult." and ".$rresult;
				
		return true; // continue hook-chain.
	}
	
	public function hRawPageViewBeforeOutput( &$rawpage, &$text )
	{
		global $wgUser;
		
		if (! $wgUser->isAllowed( "raw") )		
		{
			$text = '';
			wfHttpError( 403, 'Forbidden', 'Unsufficient access rights.' );
			return false;
		}
		
		return true; // continue hook-chain.
	}
} // end class definition.
//</source>