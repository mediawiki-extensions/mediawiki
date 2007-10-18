<?php
/**
 * @author Jean-Lou Dupont
 * @package PageMetaData
 * @version $Id$
*/
//<source lang=php>
class PageMetaData
{
	const template_file_name = 'PageMetaData.template.wikitext';
	static $tfile = null;
	const rsection = '$restrictions$';

	public function __construct()
	{
		self::$cdir = dirname(__FILE__);
		self::$tfile = self::$cdir.'/'.self::template_file_name;
	}
	
	/**
	 * Main Hook
	 */
	public function hArticleProtect( &$article, &$user, $limit, $reason )
	{
		$this->doUpdate( $article );	
		return true;
	}
	/**
	 */
	protected function doUpdate( &$a )
	{
		$template = $this->loadTemplate();
		
		$data = $this->fillTemplate( $template, $a );
		
		$this->updatePage( $a, $data );		
	}
	/**
	 */
	protected function updatePage( &$article, &$data )
	{
		$pageTitle = $article->mTitle->getText().'.meta';
		$title = Title::newFromText( $pageTitle, $article->mTitle->getNamespace() );
		
		$a = new Article( $title );
		$new = ($a->getId()==0) ? true:false;
		
		if ( is_null($a) )
		{
			// this shouldn't happen anyways.
			throw new MWException( __METHOD__ );
		}
		else
		{
			if ($new)
				$flags = EDIT_NEW | EDIT_DEFER_UPDATES;
			else
				$flags = EDIT_UPDATE | EDIT_DEFER_UPDATES;
			$a->doEdit( $data, ' ', $flags );			
		}
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	/**
	 */
	protected function loadTemplate()
	{
		return @file_get_contents( self::$tfile );	
	}	 
	/**
	 */
	protected function fillTemplate( &$template, &$article )	
	{
		$data = $this->getRestrictions( $article->mTitle );
		$data = str_replace( self::rsection, $data, $template );
		return $data;
	}
	/**
	 */
	protected function getRestrictions( &$title )
	{
		$data = null;
		$title->loadRestrictions();
		if (!empty( $title->mRestrictions ))
			$data = $this->getRestrictionsSection(	$title->mRestrictions, 
													$title->mRestrictionsExpiry,
													$title->mCascadeRestriction
													 );
		return $data;													 
	}
	/**
	 */
	function getRestrictionsSection( &$restrictions, $expiry, $cascading )
	{
		$result = '';
		foreach( $restrictions as $restrictionType => &$levels )
			foreach( $levels as $level)
				$result .= "    <restriction type='".$restrictionType."' level='".$level.
							"' expiry='".$expiry."' cascading='".$cascading."' />\n";

		return $result;
	}
	
}//end class
//</source>