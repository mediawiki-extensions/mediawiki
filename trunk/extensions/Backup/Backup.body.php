<?php
/**
 * @author Jean-Lou Dupont
 * @package Backup
 */
// <source lang=php>
class Backup
{
	const thisType = 'hook';
	const thisName = 'Backup';
	
	//
	var $rc;
	var $op; // current backup_operation
	var $executeDeferredInRcHook;
		
	/**
	 */
	public function __construct() 
	{
		$this->op = null;
		$this->executeDeferredInRcHook = false;
	}
	
	/**
		Handles article creation & update
		
		Creation and Update operations can not be discerned;
		they are handled both as 'edit'.
	 */	
	public function hArticleSaveComplete( &$article, &$user, &$text, &$summary, $minor, 
											$dontcare1, $dontcare2, &$flags,
											 &$revision = null /* MW1.11 */)
	{
		$this->op = new backup_operation(backup_operation::action_edit,
										$article,
										true,	// include last revision text
										$this->rc->mAttribs['rc_id'],
										$this->rc->mAttribs['rc_timestamp']											
									 );		 
		$this->doNotify();
		return true;
	}

	/**
		Handles Article Deletion
		
		WARNING: If ArticleDelete hook fails, we might have some stranded resources
		e.g. temporary file
	 */
	public function hArticleDelete( &$article, &$user, $reason )
	{
		$this->op = new backup_operation(backup_operation::action_delete,
										$article,
										false,	// don't include last revision text
										null,	// we don't know the id just yet
										null	// nor the timestamp
								 		);
		// we can't complete the execution in this hook. 
		// We are missing some parameters.
		return true;
	}
	/**
		Handles Article Deletion
		along with hArticleDelete
	 */
	public function hArticleDeleteComplete( &$article, &$user, $reason )
	{
		// complete the 'op' object with the missing data.
		$this->op->setIdTs(	$this->rc->mAttribs['rc_id'], 
							$this->rc->mAttribs['rc_timestamp'] );
		
		$this->doNotify();
		return true;
	}
	
	/**
		Handles article move.
		
		This hook is often called twice:
		- Once for the page
		- Once for the 'talk' page corresponding to 'page'
		Each time a new 'backup_operation' is created and the
		custom hook 'backup' is fired.
		
		NOTES:
		- RecentChange_save hook is called before this hook
		  in 'Title::moveToNewTitle' through the log entry save functionality
	 */
	public function hSpecialMovepageAfterMove( &$sp, &$oldTitle, &$newTitle )
	{
		$this->op = new backup_operation(backup_operation::action_move,
										$newTitle,
										true,	// include last revision text
										$this->rc->mAttribs['rc_id'],
										$this->rc->mAttribs['rc_timestamp']											
									 );
									 
		$this->op->setSourceTitle( $oldTitle );
		$this->doNotify();
		return true;		
	}
	
	/**
		File Upload	
	 */
	public function hFileUpload( &$img )
	{
		$this->op = new backup_operation(backup_operation::action_createfile,
										$article,
										false,	// do not include last revision text
										null,
										null										
									 );
		
		// We are missing some parameters that will only be available
		// when then 'RecentChange' event is triggered.
		$this->executeDeferredInRcHook = true;		
		return true;		
	}
	
	/**
		Just send the 'page' details which contain the 'restrictions'
		aka 'protection' information.	
	 */
	public function hArticleProtectComplete( &$article, &$user, &$limit, &$reason )
	{
		$this->op = new backup_operation(backup_operation::action_protect,
										$article,
										false,	// do not include last revision text
										null,
										null										
									 );
									 
		$this->executeDeferredInRcHook = true;		
		
		return true;
	}
	/**
		Uses the custom hook 'ImageDoDeleteBegin' defined in [[Extension:ImagePageEx]]
		to trap image object deletion.	
	 */
	public function hImageDoDeleteBegin( &$img_page )
	{
		$this->op = new backup_operation(backup_operation::action_imagedelete,
										$img_page,
										false,	// do not include last revision text
										null,
										null										
									 );
		$this->executeDeferredInRcHook = true;		
		return true;	
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	
	
	/**
		Just grab the essential parameters we need to complete the transaction.
	 */
	public function hRecentChange_save( &$rc )
	{
		$this->rc = $rc;
		
		// Log entry case: useful for the following events:
		// - general log entry
		// - delete event (image / file)
		if ($this->rc->mAttribs['rc_type'] == RC_LOG /*defined in Defines.php*/)
		{
			$this->op = new backup_operation(backup_operation::action_log,
											$rc );
			$this->doNotify();
			
			// nothing else todo
			return true;
		}
		
		/*
			Used in the following cases:
			- FileUpload
			- Article Protect
			- Image delete
		 */
		if ($this->executeDeferredInRcHook)
		{
			$this->op->setIdTs(	$this->rc->mAttribs['rc_id'], 
								$this->rc->mAttribs['rc_timestamp'] );
			$this->doNotify();
		}
		return true;		
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%	

	/**
		This method finally fires the 'backup' event.
	 */
	public function doNotify()
	{
		wfRunHooks( 'Backup', array( &$this->op ) );		
	}
	
} // end class
//</source>
