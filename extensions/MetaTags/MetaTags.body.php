<?php
/**
 * @author Jean-Lou Dupont
 * @package MetaTags
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class MetaTags
{
	const thisType = 'other';
	const thisName = 'MetaTags';
	
	/**
	 * {{#meta: HTTP-EQUIV | CONTENT [| NAME] }}
	 */
	public function mg_meta( &$parser, $httpEquiv, $content, $name='' )
	{
		if (!self::checkExecuteRight( $parser->mTitle ))
			return 'MetaTags: '.wfMsg('badaccess');
		
		$httpEquiv = trim( htmlspecialchars( $httpEquiv ) );
		$content = trim( htmlspecialchars( $content ) );
		
		if (!empty( $name ))
		{
			$name = trim( htmlspecialchars( $name ) );
			$nameAtt = "name=\"$name\"";
		}
		$parser->mOutput->addHeadItem("<meta http-equiv=\"$httpEquiv\" content=\"$content\" $nameAtt >\n");
	}
	/**
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
