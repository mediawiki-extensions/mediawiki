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
	static $cdir = null;

	public function __construct()
	{
		self::$cdir = dirname(__FILE__);
		self::$tfile = self::$cdir.'/'.self::template_file_name;
	}
	
	/**
	 * Main Hook
	 */
	public function hArticleProtectComplete( &$article, &$user, $limit, $reason )
	{
		$this->doUpdate( $article, $limit );	
		return true;
	}
	/**
	 */
	protected function doUpdate( &$a, $limit )
	{
		$template = $this->loadTemplate();
		
		$data = $this->fillTemplate( $template, $a, $limit );
		
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
	protected function fillTemplate( &$template, &$article, $limit )	
	{
		$data = $this->getRestrictions( $limit );
		$data = str_replace( self::rsection, $data, $template );
		return $data;
	}
	/**
	 */
	protected function getRestrictions( &$limit )
	{
		$result = null;

		if (empty( $limit ))
			return null;

		foreach( $limit as $action => &$restrictions )
			$result .= "    <restriction type='".$action.
						"' level='".$restrictions."' />\n";
//						"' expiry='".$expiry."' cascading='".$cascading."' />\n";

		return $result;
	}
	
}//end class
//</source>