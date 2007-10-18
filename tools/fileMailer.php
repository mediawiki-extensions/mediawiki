<?php
//<source lang=php>

echo "fileMailer: using file '".$argv[1]."'\r\n";
$contents = @file_get_contents( $argv[1] );

$msg = MPmsg::build( 'application/gzip', $argv[1], $contents );

$headers = 'From: wiki_admin@jldupont.com' . "\r\n" .
'Reply-To: wiki_admin@jldupont.com'. "\r\n" .
'X-Mailer: PHP/' . phpversion() . "\r\n" .
'Content-Type: multipart/mixed;' .
' boundary="' . MPmsg::getBoundary() . '"' . "\r\n";

echo "Sending ... ";
$result = mail(	'wiki_backup@jldupont.com', 
				'jldupont.com backup', 
				$msg,
				$headers);

echo ($result) ? "successful\n":"failed!\n";
die( $result );

class MPmsg
{
	static $boundary = null;
	
	public function getBoundary()
	{
		return self::setBoundary();
	}
	public static function setBoundary()
	{
		if (self::$boundary === null)
			self::$boundary = '==_MessageBoundary_'.md5(uniqid()).'_==';
		return self::$boundary;
	}
	
	public static function build( $mime_type, $name, &$data )
	{
		self::setBoundary();
		$att_data = self::encode( $data );
		
		$message = "This is a multi-part message in MIME format.\r\n\r\n".
	            "--".self::$boundary."\r\n".
	            "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n".
				"Content-Disposition: inline\r\n" .
	            "Content-Transfer-Encoding: 7bit\r\n".
	            "Here is the backup file: "."\r\n\r\n";
		
		$message .= '--'.self::$boundary."\r\n";
		
		$message .= 'Content-Type: '.$mime_type.';' . "\r\n" .' name="'.$name.'"' .
					'Content-Transfer-Encoding: base64' . "\r\n" .
					"Content-Disposition: attachment;\r\n" . 
					" filename=\"".$name."\"\r\n\r\n" .
					$att_data."\r\n" .
		             "--".self::$boundary."\r\n";
		return $message;
	}
	public static function encode( &$data )
	{
		return chunk_split(base64_encode($data));
	} 
}

//<source>