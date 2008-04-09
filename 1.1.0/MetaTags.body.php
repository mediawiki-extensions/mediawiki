<?php
/**
 * @author Jean-Lou Dupont
 * @package MetaTags
 * @version 1.1.0
 * @Id $Id: MetaTags.body.php 1005 2008-04-09 18:03:40Z jeanlou.dupont $
 */
//<source lang=php>
class MetaTags
{
	const thisType = 'other';
	const thisName = 'MetaTags';
	
	/**
	 * {{#link: rel | type | href }}
	 */
	public function mg_meta( &$parser, $rel, $type, $href )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'MetaTags: '.wfMsg('badaccess');
		
		$_rel  = $this->process( 'rel',  $rel );
		$_type = $this->process( 'type', $type );
		$_href = $this->process( 'href', $href );

		$parser->mOutput->addHeadItem("<link{$_rel}{$_type}{$_href}>\n");
	}
	
	/**
	 * {{#meta: HTTP-EQUIV | CONTENT [| NAME] }}
	 */
	public function mg_meta( &$parser, $httpAtt, $contentAtt, $nameAtt='' )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'MetaTags: '.wfMsg('badaccess');
		
		$http = $this->process( 'http-equiv', $httpAtt );
		$cont = $this->process( 'content', $contentAtt );
		$name = $this->process( 'name', $nameAtt );

		$parser->mOutput->addHeadItem("<meta{$http}{$cont}{$name}>\n");
	}
	/**
	 * Processes an HTML tag attribute for security reasons
	 * @protected 
	 */
	protected function process( $att, $input )
	{
		$out = trim( htmlspecialchars( $input ) );
		
		if (empty( $out ))
			return '';
			
		return " $att=\"$out\"";
	}	 
	/**
	 * Verifies if the current user has the required right to use the
	 * parser functions defined in this extension
	 * 
	 *	1- IF the page is protected for 'edit' THEN allow execution
	 *	2- IF the page's last contributor had the 'meta' right THEN allow execution
	 *	3- ELSE deny execution
	 */
	private static function checkExecuteRight( &$title )
	{
		global $wgUser;
		
		if ($wgUser->isAllowed('meta'))
			return true;
		
		if ($title->isProtected('edit'))
			return true;
		
		// Last resort; check the last contributor.
		$rev    = Revision::newFromTitle( $title );
		
		$user = User::newFromId( $rev->mUser );
		$user->load();
		
		if ($user->isAllowed( 'meta' ))
			return true;
		
		return false;
	}
	
} // end class
//</source>
