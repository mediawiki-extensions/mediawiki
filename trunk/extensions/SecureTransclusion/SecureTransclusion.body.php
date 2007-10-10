<?php
/**
	@author Jean-Lou Dupont
	@package SecureTransclusion	
 */
//<source lang=php>
class SecureTransclusion
{
	const thisType = 'other';
	const thisName = 'SecureTransclusion';
	
	#static $marker  = "\xfc__%index__\xfd";
	#static $pattern = "/\xfc__(.*)__\xfd/si";
	
	#var $liste;
	
	public function __construct() 
	{
		#$this->liste = array();
	}
	
	public function mg_strans( &$parser, $iwpage )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'SecureTransclusion: '.wfMsg('badaccess');
		
		$title = Title::newFromText( $iwpage );
		if (is_null( $title ) || (!$title->isTrans()))
			return 'SecureTransclusion: '.wfMsg("importbadinterwiki");
		
		$uri = $title->getFullUrl();
		$text = Http::get( $uri );
			
		#$index = count( $this->liste );
		#$marker = str_replace( '%index', $index, self::$marker );
		#$this->liste[] = array('iw' => $iw, 'page' => $page );
		
		return $text;
	}
	/**
	 */
	#public function hParserAfterTidy( &$parser, &$text ){}	
	/**
		1- IF the page is protected for 'edit' THEN allow execution
		2- IF the page's last contributor had the 'strans' right THEN allow execution
		3- ELSE deny execution
	 */
	private static function checkExecuteRight( &$title )
	{
		if ($title->isProtected('edit'))
			return true;
		
		global $wgUser;
		if ($wgUser->isAllowed('strans'))
			return true;
		
		// Last resort; check the last contributor.
		$rev    = Revision::newFromTitle( $title );
		
		$user = User::newFromId( $rev->mUser );
		$user->load();
		
		if ($user->isAllowed( 'strans' ))
			return true;
		
		return false;
	}
		
} // end class
//</source>
