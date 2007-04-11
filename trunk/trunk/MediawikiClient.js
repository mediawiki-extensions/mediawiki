/**
 * MediawikiClient.js
 * @author Jean-Lou Dupont
 */

MediawikiClient = function()
{
	// declare the custom event used to signal
	// status update re: document loading
	this.onDocStatusChange =	new YAHOO.util.CustomEvent( "onDocStatusChange" );
	
	// Load the global variables that should have already
	// been declared in the page.
	// E.g.:
	// var xmlsrcpage="Admin:Test_ArticleEx2"; 
	// var xslsrcpage="Admin:Test_ArticleEx2";
	//
	/* There is of course the variables declared by default by Mediawiki
	 * ***************************************************************** 
	 		var skin = "monobook_bc";
			var stylepath = "/skins";

			var wgArticlePath = "/index.php?title=$1";
			var wgScriptPath = "";
			var wgServer = "http://localhost";
                        
			var wgCanonicalNamespace = "Admin";
			var wgNamespaceNumber = 100;
			var wgPageName = "Admin:Test_ArticleEx2";
			var wgTitle = "Test ArticleEx2";
			var wgArticleId = 2085;
			var wgIsArticle = true;
                        
			var wgUserName = "Bluecortex";
			var wgUserLanguage = "en";
			var wgContentLanguage = "en";
	 */

	YAHOO.util.Event.onAvailable('xmltable',  this.process,       this);
	//YAHOO.util.Event.onAvailable('xmlisland', this.processIsland, this);
	
	// If no xmlsrcpage is defined, then use the current source page
	// as data source for the XML data.
	if (typeof xmlsrcpage !== 'undefined')
		{ var u1= wgArticlePath.replace('$1', xmlsrcpage+".xml" ); }
	else
		{ var u1= wgArticlePath.replace('$1', wgPageName+".xml" ); }
		
	if (typeof xslsrcpage !== 'undefined')
	{ 
		var u2= wgArticlePath.replace('$1', xslsrcpage+".xsl" );
		this.sxslurl = encodeURI( u2 );
	}
	
	this.sxmlurl = encodeURI( u1 );

	articlePath = wgArticlePath.replace('$1','');

	this.xc = new XMLclient( this.sxmlurl, this.sxslurl, this.onDocStatusChange, articlePath );
};
MediawikiClient.prototype =
{
	process: function(me)
	{
		var tEl = document.getElementById('xmltable');

		me.xc.doprocess(tEl);
	},
	
	processIsland: function(me)
	{
		alert('Found XML data island!');
	}
};

var mw = new MediawikiClient();