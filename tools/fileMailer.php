<?php
//<source lang=php>

class MPmsg
{
	static $boundary = null;
	
	public static function setBoundary()
	{
		if (self::$boundary === null)
			self::$boundary = '==_MessageBoundary_'.md5(uniqid()).'_==';
	}
	
	public static function build( $mime_type, $name, &$data )
	{
		self::setBoundary();
		$att_data = self::encode( $data );
		
		$message = "This is a multi-part message in MIME format.\r\n\r\n".
	            "--".self::$boundary."\r\n".
	            "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n".
	            "Content-Transfer-Encoding: 7bit\r\n".
	            $txt."\r\n\r\n".
	            "--".self::$boundary."\r\n";
		$message .= 'Email body follows:'."\r\n\r\n";
		
		$message .= self::$boundary."\r\n";
		
		$message .= 'Content-Type: '.$mime_type.';' . "\r\n" .
					'Content-Transfer-Encoding: base64' . "\r\n" .
					"Content-Disposition: attachment;\r\n" . 
					" filename=\"".$fileatt_name."\"\r\n\r\n" .
					$att_data."\r\n" .
		             "--".self::$boundary."\r\n";
		
	}
	public static function encode( &$data )
	{
		return chunk_split(base64_encode($data));
	} 
}

//<source>