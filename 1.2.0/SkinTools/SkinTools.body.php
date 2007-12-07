<?php
/**
 * @author Jean-Lou Dupont
 * @package ParserExt
 * @subpackage SkinTools
 * @version 1.2.0
 * @Id $Id: SkinTools.body.php 724 2007-12-07 20:17:12Z jeanlou.dupont $
 */
//<source lang=php>
class SkinTools
{
	const thisName = 'SkinTools';
	const thisType = 'other';

	var $actions;
	var $actionsToRemove;
	var $actionsToAdd;

	// Our class defines magic words: tell it to our helper class.
	public function __construct()
	{	
		$this->actions  = null;
		$this->actionsToRemove = null;
		$this->actionsToAdd = null;		
	}
	public function mg_clearactions( &$parser )
	{
		$this->actions = false;
		wfRunHooks( 'clearSkinTabActions', array(/*no parameters*/) );
	}
	/**
		List of actions to remove from the current page.
	 */
	public function mg_removeactions( &$parser )
	{
		$params = StubManager::processArgList( func_get_args(), true );
		if (isset( $params ))
			foreach( $params as $actionToRemove )
				$this->actionsToRemove[] = $actionToRemove;
	}

	public function mg_addaction( &$parser, $action, $actionText, $actionSubPage = null, $actionOverride = null )
	{
		if (empty( $action ) || empty( $actionText))
			return 'SkinTools: invalid parameters';

		$this->actionsToAdd[] = array(	'action' 		=> $action, 
										'actionText' 	=> $actionText,
										'actionSubPage'	=> $actionSubPage,
										'actionOverride' => $actionOverride
									);
	}	
	/**
		For modifying the 'action' toolbar on a page.
	 */
	public function hSkinTemplateTabs( &$st , &$content_actions )
	{
		// check if we are asked to remove all actions
		// from the page.
		if ( $this->actions === false )
			{ $content_actions = null; return true; }
		
		if (!empty( $this->actionsToRemove ))
			foreach( $this->actionsToRemove as $action )
				unset( $content_actions[$action] );

		if (!empty( $this->actionsToAdd ))
			foreach( $this->actionsToAdd as $actionDetails )
			{
				if (!empty($actionDetails['actionSubPage']))
					$title = Title::newFromText( $st->mTitle->getPrefixedText().'/'.$actionDetails['actionSubPage'] );
				else
					$title = $st->mTitle;
				
				if (!empty($actionDetails['actionOverride']))
					$contentTab = $actionDetails['actionOverride'];
				else
					$contentTab = $actionDetails['action'];
				
				// skip if the user isn't allowed the action.
				$tAction = ($actionDetails['action'] =='view') ? 'read': $actionDetails['action'];
				$tAction = ($actionDetails['action'] =='')     ? 'read': $actionDetails['action'];				
				
				global $wgUser;
				if ( !$wgUser->isAllowed($tAction) )
					continue;
					
				if (defined('BIZZWIKI'))
					if ( !$wgUser->isAllowed($tAction, $title->getNamespace(), $title->getDBkey() ))
						continue;
				
				$query = ( $actionDetails['action'] == 'read' ) ? '':'action='.$actionDetails['action'];
				
				$content_actions[ $contentTab ] = array(
					'text' => $actionDetails['actionText'],
					'href' => $title->getLocalUrl( $query )
				);
			}
		return true;
	}
	
} // end class
//</source>