<?php
/*
 * XMLclass.php
 * 
 * MediaWiki extension
 * @author: Jean-Lou Dupont (http://www.bluecortex.com)
 *
 * DEPENDANCIES:
 * -- (optional) "variables extension" for inter-extension
 *    communication with this one.
 *
 * -- "ArticleEx extension" for easy integration
 *    with "Xinclude" processing when required.
 *    
 * -- "ArticleCacheClass" for easy article loading.
 *
 * FEATURES:
  * 1) Mediawiki page can be used as XML/XSL data source
 *    and referenced in Xinclude directives (as example). 
 *
 *    For each local references to MW pages, this extension
 *    replaces with a valid local URL. This provides the client
 *    processor the capability to fetch the XML objects from the
 *    Mediawiki page database.
 *
 *    Of course, this process is facilitated through the "ArticleEx" extension.
 *
 * 2) Ability to add Javascript variables to the output page for easier
 *    client-side processing. 
 *    E.g <xml src='Main:SomeXMLdatasourcePage' />
 *    --> Will result in 'var xmlsrcpage="Main:SomeXMLdatasourcePage"; '
 *    Js variables to be added.
 *
 * 3) Capability to 'view' XML/XSL data sources in the same page as the
 *    client-side rendering occurs. See feature #4 for additional details.
 *
 * 4) Capability to integrate with other extensions (such as GeSHi) for
 *    rendering the <xml> / <xsl>  data sections.  Use the <xml view=x>
 *    or <xsl view=y> directives.
 *
 * USAGE NOTES:
 * 1) If one needs to embed an XML data source & use the <xml src=".." /> directive,
 *    then the latter directive must be placed AFTER the <xml>...</xml> data source
 *    in the Mediawiki page. This limitation has to do on how the sections are retrieved
 *    by the "ArticleEx" class.
 * 2) Same note as 1) applies for XSL data sources/directives. 
 *
 * GENERAL NOTES:
 * 1) A nice client-side cross-browser XML/XLS processor is "sarissa" 
 * which can be found at: http://sarissa.sourceforge.net/doc/
 * 2) The global JS variables accessible on each Mediawiki pages:
 *    E.g.:
		<script type= "text/javascript">
			var skin                 = "monobook_bc";
			var stylepath            = "/skins";

			var wgArticlePath        = "/index.php?title=$1";
			var wgScriptPath         = "";
			var wgServer             = "http://localhost";
                        
			var wgCanonicalNamespace = "Admin";
			var wgNamespaceNumber    = 100;
			var wgPageName           = "Admin:Test_ArticleEx2";
			var wgTitle              = "Test ArticleEx2";
			var wgArticleId          = 2085;
			var wgIsArticle          = true;
                        
			var wgUserName           = "Bluecortex";
			var wgUserLanguage       = "en";
			var wgContentLanguage    = "en";
		</script>
 *
 * TODO:
 * 1- deal more neatly with missing MW:include references
 * 2- add treatment for error conditions
 * 
 */

class XMLclass
{
	// collection.
	var $data;
	
	// Parameters to the stylesheet
	var $params;
	
	// 
	var $view;
	
	// variables extension shortcut.
	var $vars;
	var $cache;
	
	var $xmloldhook;
	var $xsloldhook;
	
	// XSL related
	var $xslPage;
	var $xslText;	
	
	var $clientSide;
	
	public static function &singleton() 
	{
		static $instance;
		if ( !isset( $instance ) ) 
			$instance = new XMLclass();
		return $instance;
	}

	// deprecated interface.
	static function getGlobalObjectName() { return "xmlObj";           }
	static function &getGlobalObject()    { return $GLOBALS['xmlObj']; }	
	
	public function XMLclass()
	{	$this->init();	}
	
	public function init()
	{
		global $wgExtVariables;
		if (isset($wgExtVariables))
			$this->vars = $wgExtVariables;

		$this->data    = null;
		$this->view    = false;
		$this->pageDataAdded = false;
		$this->tableElementAdded = false;
		$this->clientSide = false;			// default is server-side processing. 
		
		$this->params = array();
		$this->xslPage = null;
		$this->xslText = null;
		
		$this->cache = &ArticleCacheClass::singleton();
		
		// let other extensions know that we found
		// an XML data island			
		$this->setvar( 'xmlobj', $this );
		$this->setvar( 'xmloldhook', $this->xmloldhook );
		$this->setvar( 'xsloldhook', $this->xsloldhook);
	}
	
	public function setXmlOldHook( $h ) { $this->xmloldhook = $h; }
	public function setXslOldHook($h){ $this->xsloldhook = $h; }
	
	private function setVar( $key, $value )
	{
		if (!empty($this->vars))
			$this->vars->setvar( $key, $value );	
	}
	
	public function getData() { return $this->data; }

// --------------------------------------------------------------------------------
// Phase 1 related
//
	/*
	 *  <xml src='...' />
	 *  <xml> ... </xml>
	 *  <xml view=x />   where x=1 --> render
	 *  <xml view=x> ... </xml>
	 */
	public function xml	( &$text, &$argv, &$parser )
	/*
	 *  By default, server-side processing is performed UNLESS
	 *  the directive "client" is given.
	 */
	{
		if (isset($argv['client']))
		{
			$this->clientSide = true;
			return '<div id="xmltable"></div>';  // add xhtml element for the JS code.
		}
			
		if (isset($argv['src']))  // for client-side processing.
		// add the client-side JS variable for locating an XML data source.
		// The Mediawiki title name should be used here.
			$this->doAddJsVar( "xmlsrcpage", $argv['src'] );
			
		if (isset($argv['view']))
			$this->view = $argv['view'];
			
		if (!empty($text))
			$this->data = $text;
			
		if ( ($this->view == '1' ) && (!empty($this->data)) )
			return $this->doview( $this->data, $argv, $parser, $this->xmloldhook );

		if (isset($argv['island'])) // v1.3 (a)
			return "<xml id='xmlisland'>".$text.'</xml>';

		// Server-Side Processing
		return $this->process( $text );
	}
	
	/*
	 *  <xsl src='...' />
	 *  <xsl> ... </xsl>
	 *  <xsl view=x />   where x=1 --> render
	 *  <xsl view=x> ... </xsl>
	 */
	
	public function xsl(&$text, &$argv, &$parser )
	{
		if (!empty($text))
			$this->xslText = $text;
	
		if (isset($argv['src']))
		// add the client-side JS variable for locating an XSL data source.
		// The Mediawiki title name should be used here.
		{
			$this->xslPage = $argv['src'];
			$this->doAddJsVar( "xslsrcpage", str_replace(" ","_", $argv['src']) );
		}
		// {{#xsl:parameter=key|value='value'}}
		if (isset($argv['parameter']))
		{
			$key   = $argv['parameter'];
			$value = $argv['value'];
			$this->params[$key] = $value;
			return; 
		}
			
		if (isset($argv['view']))
		{
			$hook = null;
			
			// try the old XSL hook first,
			// if no success, maybe a GeSHi-like extension
			// is lurking in the xmloldhook...
			if (!empty($this->xsloldhook))
				$hook = $this->xsloldhook;
			elseif (!empty($this->xmloldhook))
				$hook = $this->xmloldhook;			
			if (!empty($hook))	
				return $this->doview($text, $argv, $parser, $hook);
		}

		// we have exhausted our options... don't render.
		return '';
	}
	public function mg_xsl( &$parser, $src )  // v1.2b feature
	{
		$e = explode("=",$src);
		if ($e[0]!='src') return;
		
		if (empty($e[1])) return;
		
		// make the title 'DB key' ready.
		$p = str_replace(" ","_", $e[1]);
		$this->doAddJsVar("xslsrcpage", $p );
		return '';
	}
	
	// #############################################################################################
	
	private function doview( &$text, &$argv, &$parser, &$hook )
	{
		// check if there was another hook that may wish to process the content
		// E.g. GeSHi syntax highlighter.
		if (!empty($hook))
			return call_user_func_array( $hook, array( &$text, &$argv, &$parser ) );

		return $text;
	}

	private function doAddJsVar( $key, $value )
	{
		global $wgOut;
		$wgOut->addScript( '<script type="text/javascript"> var '.$key.'="'.Xml::escapeJsString($value).'"; </script>'); 
	}

	private function process( &$text )
	/*
	 *  Server-side XSLT processing.
	 */
	{
		if (empty( $text ))
			return;
			
		# get the XSL text
		if (!empty( $this->xslPage ))
		{
			$xslArticle = $this->cache->getArticle( $this->xslPage );
			if (!is_object($xslArticle))
				return "XML extension: error loading XSL page <br/>";
				
			$t = $this->cache->getArticleContent( $this->xslPage );
			
			$this->xslText = $this->extractSection( "xsl", trim($t) );
		}
		# Create the XSL document
		$xsl = new DOMDocument;
		try { $xsl->loadXML( $this->xslText ); }
		catch(Exception $e) { return "XML extension: error loading XSL code <br/>"; }
		
		# Instantiate the Processor
		$proc = new XSLTProcessor;
		
		try      { $proc->importStyleSheet( $xsl );	} 
		catch(Exception $e) { return "XML extension: error importing stylesheet <br/>"; }

		# Set Parameters
		foreach( $this->params as $key => $value )
			$proc->setParameter('', $key, $value );

		#By default, include the 'wgArticlePath' parameter
		global $wgArticlePath;
		$path = str_replace('$1','', $wgArticlePath);
		$proc->setParameter('', 'articlepath', $path );
		
		# Process the include directives.
		$this->hInclude( $text );
		
		# Create the XML document
		$xml = new DOMDocument;
		$xml->loadXML( $text );
		
		# Apply the transform
		try { $result = $proc->transformToXML( $xml ); }
		catch(Exception $e) { return "XML extension: error transforming the document <br/>"; }
		
		return $result;
	}

// -------------------------------------------------------------------------------------------------
// Phase 2 related
//
// When an XML / XSL data source is extracted from a MW page,
// this handler is called.  Mediawiki title name are substituted
// for local URL based ones.
// E.g.  "MW:Admin:XML_datasource"  becomes "/index.php?title=Admin:XML_datasource"
// 
// Obviously, the local URL depends on the particular Mediawiki installation details.
// This substitution process is especially useful when dealing with Xinclude directives
// whereby additional XML data sources are located also in the page database.
//
	public function hArticleViewEx( &$aex, &$type, &$content )
	/*
	 *  This function is called from ArticleEx::view()
	 *  when an XML page must be returned.
	 * 
	 * $aex     : instance of ArticleExClass (extends Article)
	 * $type    : string: xml, xsl, js etc.
	 * $content : content about to be returned to the client browser.
	 *
	 */
	{
		// FIRST, make sure we are dealing with an XML type !
		// This function is called by the ArticleEx::view() function
		if ( ($type!='xml') && ($type!='xsl') )
			return;

		// Set header ContentType			
		global $wgMimeType;
		$wgMimeType = "text/xml";

		$this->hInclude( $content );

		// Process all the MW references left around.
		XMLclass::processMWreferences( $content );
	}

	public static function processMWreferences( &$text )
	{
		// let's find all the "MW: " occurences in the text.
		// and swap them for local URL references instead.
		//
		$p = '/(?:"MW:)(.*)(?:")/siU';
		$r = preg_match_all( $p, $text, $m );
		
		if ($r == 0) return;
			
		// at this point, we have matches => process!
		// $m[x][0] -> complete matches
		// $m[x][1] -> subpatterns
		
		foreach($m[0] as $index => $el)
		{
			// construct a valid MW title name
			$t =   Title::newFromText($m[1][$index]);
			$url = $t->getLocalURL();
			
			// use a validated name
			$n = $t->getEscapedText();
	
			$text = preg_replace("/MW:".$n."/", $url, $text);
		}
	}
	public function hInclude( &$content )
	{
		// format supported:
		// <mw:include href="" />
		$p = '/<mw:include href=(?:\"|\')(.*)(?:\"|\')(?:.*)\/>/siU';
		preg_match_all($p, $content, $m );
		
		/*
		Example for: 
			<mw:include href="1" />
 			<mw:include href="2" />

		array(2) {
				  [0]=>
				  array(2) {
				    [0]=>    string(23) "<mw:include href="1" />"
				    [1]=>    string(23) "<mw:include href="2" />  }
				  [1]=>
				  array(2) {
				    [0]=>    string(1) "1"
				    [1]=>    string(1) "2"  }
				}
		*/

		// for each match, fetch the article page
		// and replace the text.
		foreach($m[1] as $index => &$el)
		{
			$a = $this->cache->getArticle( $el );
			if (is_object($a))     // v1.3b
				$id = $a->getID();
			else $id= 0;
			
			// put in [2] the article's status (it's ID)
			$m[2][$index] = $id;/* = $a->getID();*/ // bug corrected in v1.3b 

			if ($id!=0) // exists or not?
			{
				// exists.
				$t = $this->cache->getArticleContent( $el );
				// and in [3] the article content (a reference to it)
				$m[3][$index] = trim($this->extractSection( "xml", $t ));
			}
			else
				$m[3][$index] = "<missing>$el</missing>";  // TODO // v1.4b
		}
		// Finally, substitute
		$content = str_replace($m[0], $m[3], $content);
	}
	public function extractSection( $tag, &$content )
	{
		# We skip over any attributes enclosed in the XML tag
		$p = "/<".$tag."(?:.*)\>(.*)(?:\<.?".$tag.">)/siU";
	 	$r = preg_match( $p, $content,  $c );
	  
	 	// return the sub-pattern matches only.
	 	if ($r==1)
			return $c[1];
			
		return null;
	}
	
} // end class definition.
?>