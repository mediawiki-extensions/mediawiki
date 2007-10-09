<?php
/*
<!--<wikitext>-->
 <file>
  <name>BackupS3.body.php</name>
  <version>$Id$</version>
  <package>Extension.BackupS3</package>
 </file>
<!--</wikitext>-->
*/
// <source lang=php>

class BackupS3
{
	const thisType = 'other';
	const thisName = 'BackupS3';
	
	/**
	 */
	public function __construct() {}

	/**
		This hook is used to provide useful information to the sysop
		in the 'Special:Version' page.
	 */
	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	{
		global $wgExtensionCredits;

		$msg = $this->getDebugMessage();
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name']==self::thisName)
					$el['description'] .= $msg;
				
		return true; // continue hook-chain.
	}
	/**
	 * TODO
	 */
	private function getDebugMessage()
	{
		return null;	
	}
	/**
		Main hook: this method is called when the event 'Backup'
		is fired from [[Extension:Backup]].
	 */
	public function hBackup( &$op )
	{
		$msg = null;

		// we are receiving the parameters from [[Extension:Backup]]:
		// We need to adapt to the task at hand here.
		$params = BackupS3Operation::translateParams( $op );
	
		$bop = new BackupS3Operation( $params );

		// If the backup operation fails,
		// it will be scheduled for a re-try		
		$result = $bop->run( $msg );
			
		return true;
	}
	
} // end class BackupS3

//</source>
