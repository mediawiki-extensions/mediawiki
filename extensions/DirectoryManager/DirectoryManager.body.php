<?php
/**
 * @author Jean-Lou Dupont
 * @package DirectoryManager
 * @version $Id$
 */
//<source lang=php>
class DirectoryManager
{
	const thisType = 'other';
	const thisName = 'DirectoryManager';
	
	static $msg;
	static $dirBase;
	
	// Constants
	const filePatternTag = "/<filepattern(?:.*)\>(.*)(?:\<.?filepattern)>/siU";
	const dirPatternTag  = "/<dirpattern(?:.*)\>(.*)(?:\<.?dirpattern)>/siU";	
	const linePatternTag = "/<linepattern(?:.*)\>(.*)(?:\<.?linepattern)>/siU";		
	
	// Template related
	var $filePattern;
	var $dirPattern;
	var $linePattern;
	
	// Variables
	var $dir;
	var $files;
	var $template;
	var $page;
	
	public function __construct() 
	{
		global $IP;
		self::$dirBase = $IP;

		global $wgCapitalLinks;
		$wgCapitalLinks = false;

		$this->filePattern = null;
		$this->dirPattern = null;
		$this->linePattern = null;
		
		global $wgMessageCache;
		foreach( self::$msg as $key => $value )
			$wgMessageCache->addMessages( self::$msg[$key], $key );		
	}
	
	public function hArticleFromTitle( &$title, &$article )
	{
		global $wgOut;
		global $wgUser;
		global $IP;
				
		// we are only interested in one particular namespace
		$ns = $title->getNamespace();
		if (NS_DIRECTORY!=$ns)
			return true;
		
		global $wgCapitalLinks;
		$wgCapitalLinks = false;
		
		global $wgRawHtml;
		$wgRawHtml = true;
		
		$titre = $title->getText();
		
		if (!$wgUser->isAllowed( 'read', $ns, $titre ))
		{
			$skin = $wgUser->getSkin();
			$wgOut->setPageTitle( wfMsg( 'directorymanager'.'title' ) );
			$wgOut->setSubtitle( wfMsg( 'directorymanager'.'view', $skin->makeKnownLinkObj( $title ) ) );
			$wgOut->addWikiText( wfMsg( 'badaccess' ) );
			
			return false; // stop normal processing flow.
		}

		$titleText = $title->getText();
		$dirName   = $IP.'/'.$titleText;

		if (is_dir( $dirName ))
			$this->dir = $dirName;
		else
			$this->dir = $IP.'/'.strtolower( substr( $titleText,0,1 )).substr( $titleText, 1 );
		
		#global $wgRequest;
		#$reqdir = $wgRequest->getText( 'title' );
		
		#echo ' this->dir: '.$this->dir."<br/>\n";
		#echo ' request title: '.$this->dir."<br/>\n";		
		
		$article = new DirectoryArticle( $title );
		
		// let mediawiki handle the articles that already exist
		if ( $article->getID() != 0 )
			return true;

		$this->dirTs = self::getDirectoryTimestamp( $this->dir );

		// Give other extensions a chance to:
		// - Cache
		// - Abort
		if (wfRunHooks( 'DirectoryManagerBegin', array( &$title, &$article, self::$dirBase, $this->dir, $this->dirTs ) ))
			$this->doDirectoryPageDisplay( $title, $article );
		
		return true;
	}
	/**
		Block edition.
	 */
	public function hCustomEditor( $article, $user )
	{
		// we are only interested in one particular namespace
		$ns = $article->mTitle->getNamespace();
		if (NS_DIRECTORY!=$ns)
			return true;

		global $wgCapitalLinks;
		$wgCapitalLinks = false;

		// there is nothing to edit in this namespace!			
		return false;	
	}
	private function doDirectoryPageDisplay( &$title, &$article )
	{
		$this->template = null;
		
		// let extensions the change to modify the template
		wfRunHooks( 'DirectoryManagerBeginPageDisplay', array( &$this, &$files, &$this->dir, &$this->template ) );		
		if ($this->template === null)
			$this->template = $this->getTemplate();
		
		$this->files = $this->getDirectoryInformation( $this->dir, self::$dirBase );

		// let extensions the chance to modify the files list.
		// Modify the 'template' parameter to add/remove wikitext
		wfRunHooks( 'DirectoryManagerBeforeCreatePage', array( &$files, $this->template ) );

		$this->page = $this->createDirectoryPage( $this->dir, self::$dirBase, $this->template, $this->files );
		
		// let extensions the chance to modify the page before it is parsed.
		wfRunHooks( 'DirectoryManagerBeforeParsePage', array( &$page ) );		
		
		$po = $this->parsePage( $this->page, $title, $article );
		
		// let extensions the chance to do last minute changes
		// before the page is actually displayed.
		wfRunHooks( 'DirectoryManagerBeforeDisplayPage', array( &$po ) );		
				
		$this->displayPage( $po );
	}
	/**
		The default template 
	 */
	private function getTemplate()
	{
		$template = wfMsgNoTrans/*ForContent*/( 'directorymanager'.'-template' );	
		
		$this->filePattern = self::extractPattern( self::filePatternTag, $template );
		$this->dirPattern  = self::extractPattern( self::dirPatternTag, $template );		
		$this->linePattern = self::extractPattern( self::linePatternTag, $template );
		
		return $template;			
	}
	private static function extractPattern( $pattern, &$text, $remove = true )
	{
		$r = preg_match( $pattern, $text, $m );

		if ($remove)
			$text = preg_replace( $pattern, '', $text );
			
		if ($r===1)
			return $m[1];
		
		return null;
	}
	private static function replaceParams( &$text )
	{
		$args = func_get_args();
		array_shift( $args );
		
		for ($i=1; $i<10; $i++)
		{
			// loop whilst we have parameters to replace
			if (!isset( $args[$i-1] ) )
				break;
			$text = str_replace( '$'.$i, $args[$i-1], $text );
		}
	}
	
	/**
		Outputs WikiText
	 */
	private function createDirectoryPage( &$dir, &$base, &$template, &$files )	
	{
		// start by adding the template content
		// to the beginning of the page.
		// The 'patterns' should have been removed by now.
		$page = $template;
		
		if (!empty( $files ))
			foreach( $files as $file )
			{
				if ( $file['name'] =='.' )
					continue;
					
				if ( $file['name'] == '..' )
					$file['name'] = self::getDotDotFile( $dir, $base );
					
				// we might have reached the root...
				if (empty($file['name']))
					continue;
					
				switch( $file['type'] )
				{
					case 'dir':
						$sline = $this->dirPattern;				
						break;
						
					case 'file':
						$sline = $this->filePattern;					
						break;
				}
				self::replaceParams( $sline, $file['name'] );
				$line = $this->linePattern;
				self::replaceParams( $line, $sline );
				
				$page .= $line;
			}

		return $page;
	}
	/**
	 */
	private function parsePage( &$text, &$title, &$article )	 
	{
		global $wgParser;
		global $wgUser;
		
		# Parse the text
		$options = new ParserOptions;
		$options->setTidy(true);
		$poutput = $wgParser->parse( $text, $title, $options );

		return $poutput;
	}

	private function displayPage( &$parserOutput )
	{
		global $wgOut;
		
		$wgOut->addParserOutput( $parserOutput );
	}
	/**
		e.g.
		array (
				0 =>
				array (
				'name' => '.',
				'type' => 'dir',
				'mtime' => 1186483435,
				),
				1 =>
				array (
				'name' => '..',
				'type' => 'dir',
				'mtime' => false,			# NOTE HERE
				),
				2 =>
				array (
				'name' => '.htaccess',
				'type' => 'file',
				'mtime' => 1181832196,
				),
				3 =>
				array (
				'name' => 'AdminSettings.php',
				'type' => 'file',
				'mtime' => 1178738087,
				),
			...
	 */
	 
	public static function getDirectoryInformation( &$dir, &$base )
	{
		$files = @scandir( $dir );
			
		$upDir = self::getDotDotFile( $dir, $base );
		$thisDir = self::getRelativePath( $dir, $base );
		
		if (empty( $files ))
			return null;
		
		foreach( $files as &$file )
		{
			$info = @filetype( $dir.'/'.$file );

			if ( '.' == $file )	$info = 'dir';
			if ( '..' == $file )$info = 'dir';

			$filename = $file;
			$mtime = @filemtime( $dir.'/'.$file );
		
			if ( $file != '.' && $file != '..' && $thisDir != '/' )
				$filename = $thisDir.'/'.$filename;

			$file = array( 'name' => $filename, 'type' => $info , 'mtime' => $mtime );
		}
	
		return $files;
	}
	/**
		Returns the filename (directory name really) correspondig to '..'
	 */
	public static function getDotDotFile( &$dir, &$base )
	{
		$d = str_replace( "\\", '/', $dir );

		$pathInfo = pathinfo( $d );
		
		$p = $pathInfo['dirname'];		

		// now remove the base.
		$s = self::getRelativePath( $p, $base );

		// make sure we haven't reached the root.
		if (empty($s))
			return '/';
			
		return $s;
	}

	public static function getRelativePath( &$dir, &$base )
	{
		$d = str_replace( "\\", '/', $dir );

		return substr( $d, strlen($base)+1 );
	}

	public static function getDirectoryTimestamp( &$dir )
	{
		return @filemtime( $dir );	
	}
	/**
		Handler for '#directory' magic word
	 */
	public function mg_directory( &$parser, $path = null, $text = null )	
	{
		if (empty( $path ))	
			return null;
			
		$title = Title::makeTitle( NS_DIRECTORY, $path );
		if (is_null( $title ))
			return null;
		
		$uri = $title->getLocalUrl();
		
		return sprintf('<html><a style="color:green" href="%s">%s</a></html>', $uri, $text);
	}
	
} // end class

class DirectoryArticle extends Article
{
	public function view()
	{
		
	}	
}
require( 'DirectoryManager.i18n.php' );
//</source>
