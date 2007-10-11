<?php
/**
 * @author Jean-Lou Dupont
 * @package BackupS3
 * @version $Id$
 */
//<source lang=php>
class BackupS3Job extends Job 
{
	var $title;

	/**
	 * Construct a job
	 * @param Title $title The title linked to
	 * @param array $params Job parameters (table, start and end page_ids)
	 * @param integer $id job_id
	 */
	function __construct( $title, $params ) 
	{
		$this->title = $title;
		
		parent::__construct( 'BackupS3', $title, $params, 0 );
		$this->params = $params;
	}
	/**
	 */
	function run() 
	{
		$this->error = null;
		$op = new BackupS3Operation( $this->params );
		$result = $op->run( $this->error );
		
		// if the operation failed, schedule a re-try
		if ( $result !== true )
			$this->insert();
		
		return $result;
	}
	
}// end class
//</source>