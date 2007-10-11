<?php
/**
 * @author Jean-Lou Dupont
 * @package BackupS3
 * @version $Id$
 */
//<source lang=php>
global $wgExtensionCredits;
$wgExtensionCredits['other'][] = array( 
	'name'    		=> 'BackupS3',
	'version'		=> '1.0.0',
	'author'		=> 'Jean-Lou Dupont',
	'url'			=> 'http://www.mediawiki.org/wiki/Extension:BackupS3',	
	'description' 	=> "Provides replication to Amazon S3.", 
);

if (class_exists('StubManager'))
{
	StubManager::createStub2(	array(	'class' 		=> 'BackupS3',
										'i18n'			=> dirname(__FILE__).'/BackupS3.i18n.php',
										'classfilename'	=> dirname(__FILE__).'/BackupS3.body.php',
										'hooks'			=> array( 'Backup', 
																'SpecialVersionExtensionTypes' ),
									)
							);
							
	// load the configuration class.
	require( dirname(__FILE__).'/BackupS3.config.php');
	
	// Register the 'special page'
	#$wgAutoloadClasses['BackupS3SP'] = dirname(__FILE__).'/BackupS3.specialpage.php';
	#$wgSpecialPages['BackupS3'] = 'BackupS3SP';
	
	global $wgJobClasses;
	global $wgAutoloadClasses;
	$wgJobClasses['BackupS3'] = 'BackupS3Job';
	$wgAutoloadClasses['BackupS3Job'] = dirname(__FILE__).'/BackupS3.job.php';
	$wgAutoloadClasses['BackupS3Operation'] = dirname(__FILE__).'/BackupS3.operation.php';
	
	try
	{
		if (!StubManager::isExtensionRegistered('Backup'))
			echo 'Extension:BackupS3 <b>requires</b> Extension:Backup';
		
	} catch(Exception $e)
	{
		echo "Extension:Backup requires a recent version of Extension:StubManager.";
	}
}
else
	echo "Extension:Backup requires Extension:StubManager";
//</source>
