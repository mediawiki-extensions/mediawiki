<?php
//<source lang=php>

echo "fileMailer: using file '".$argv[1]."'\r\n";
$contents = @file_get_contents( $argv[1] );

$msg = MPmsg::build( 'application/gzip', $argv[1], $contents );

$headers = 'From: wiki_admin@jldupont.com' . "\n" .
'Reply-To: wiki_admin@jldupont.com'. "\n" .
'MIME-Version: 1.0'. "\n" .
'X-Mailer: PHP/' . phpversion() . "\n" .
'Content-Type: multipart/mixed;' .
' boundary="' . MPmsg::getBoundary() . '"' . "\n";

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
		
		$message = "This is a multi-part message in MIME format.\n\n".
	            "--".self::$boundary."\n".
	            "Content-Type: text/plain; charset=\"iso-8859-1\"\n".
				"Content-Disposition: inline\n" .
	            "Content-Transfer-Encoding: 7bit\n".
	            "Here is the backup file: "."\n\n";
		
		$message .= '--'.self::$boundary."\r\n";
		
		$message .= 'Content-Type: '.$mime_type.';' . "\n" .' name="'.$name.'"' . "\n".
					'Content-Transfer-Encoding: base64' . "\n" .
					"Content-Disposition: attachment;\n" . 
					" filename=\"".$name."\"\n\n" .
					$att_data."\n" .
		             "--".self::$boundary."\n";
		return $message;
	}
	public static function encode( &$data )
	{
		return chunk_split(base64_encode($data));
	} 
}

//<source>