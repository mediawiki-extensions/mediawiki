<?php
/**
 * @author Jean-Lou Dupont
 * @package Backup
 */
//<source lang=php>

/*
	EDIT:    rc_id, rc_timestamp, ns, title, rev id, 
	DELETE:  rc_id, rc_timestamp, ns, title, rev id,
	PROTECT: rc_id, rc_timestamp, ns, title, rev id, 
	MOVE:    rc_id, rc_timestamp, ns, title, rev id, source title	

*/

class backup_operation
{
		// Constants
	const action_none       = 0;
#	const action_create     = 1; // TBD
	
		// page related
	const action_edit       = 2;
	const action_delete     = 3;
	const action_move       = 4;
	const action_protect    = 5;
		
		// file related
	const action_createfile = 6;
	const action_deletefile = 7;
	
		// log related
	const action_log		= 8;
	
		// image related
	const action_imagedelete = 9;
	
	static $deferred = array(	self::action_protect, 
								self::action_imagedelete, 
								self::action_delete );
	
	// Commit Operation parameters
	var $includeRevision;
	var $deferralRequired;

	// parameters	
	var $action;		// internal to this extension.
	var $object;		// mainly for debugging
	var $id;			// rc_id
	var $timestamp;		// rc_timestamp
	var $ns;			// rc_namespace
	var $titre;			// rc_title
	var $revId;			// revision ID of page.

	var $sourceTitle;	// for move action
	var $sourceTitleNs; // for move action
	var $sourceTitleName;// for move action
		
	// Object references
	var $revision;
	var $title;

	var $history;		// current or full
	
	var $text;
	
	public function __construct( $action, &$object, $includeRevisionText = false, $id=null, $ts=null )
	{
		$this->action = $action;
		$this->object = & $object;
		
		$this->includeRevisionText = $includeRevisionText;
		$this->revision = null;
		$this->title = null;		
		// for page move.
		$this->sourceTitle = null;

		// get the critical information.		
		$this->getNsTitleRevision( $object, $this->ns, $this->titre, $this->revision, $this->title );
	
		if ($this->revision instanceof Revision)
			$this->revId = $this->revision->getId();
	
		$this->deferralRequired = $this->getDeferralState( );

		// an 'rc' object comes in when the transaction
		// is related to a 'logging' event.
		if ( $object instanceof RecentChange )
		{
			$this->id = $object->mAttribs['rc_id'];
			$this->timestamp = $object->mAttribs['rc_timestamp'];
		}
		else
		{
			$this->id = $id;
			$this->timestamp = $ts;
		}
	}
	private function getNsTitleRevision( &$object, &$ns, &$titre, &$rev, &$title )
	{
		if ( $object instanceof RecentChange )
		{
			$ns = $object->mAttribs['rc_namespace'];
			$title = $object->mAttribs['rc_title'];	
			return true;
		}
		// For Image Delete, the object will still be
		// an instanceof Article
		if ( $object instanceof Article )
		{
			$title = $object->mTitle;
			$rev = $object->mRevision;
		}
			
		// cases: page move
		if ( $object instanceof Title )
		{
			$title = $object;
			
			// upon page move, the revision id
			// for this object will be overriden
			// when the 'setSourceTitle' method is invoked.
			$rev = $object->getLatestRevID();
		}

		$ns = $title->getNamespace();
		$titre = $title->getText();
		
		return true;
	}
	public function setIdTs( $id, $ts ) { $this->id = $id; $this->timestamp = $ts; }
	public function getDeferralState( ) { return in_array( $this->action, self::$deferred ); }
	
	/**
		This method is used when handling the 'page move' operation.
		The 'revision id' will be set as the 'latest revision' from the source
		title object.
	 */
	public function setSourceTitle( &$t ) 
	{ 
		$this->sourceTitle = $t; 
		$this->sourceTitleNs   = $t->getNamespace();
		$this->sourceTitleName = $t->getDBkey();
		$this->revision = $t->getLatestRevID();
	}
	
} // end class

//</source>