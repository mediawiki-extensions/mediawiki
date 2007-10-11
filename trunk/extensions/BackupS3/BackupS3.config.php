<?php
/**
 * @author Jean-Lou Dupont
 * @package BackupS3
 * @version $Id$
 */
//<source lang=php>
class BackupS3Config
{
	// there can not be any sensible default:
	// the user of this extension MUST set these.
	static $bucket_name = null;
	static $secret_key  = null;
	static $access_key  = null;
}
//</source>