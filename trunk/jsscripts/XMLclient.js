/*
 * XMLclient.js
 *  
 * Author: Jean-Lou Dupont  (www.bluecortex.com)
 * $Id$
 * $LastChangedRevision$
 *  
 *  Purpose:  This JS class serves as client side
 *  ========  XML processor helper class for "Sarissa", the cross-browser
 *            XML/XSL processor class.
 *    
 *  DEPENDANCIES:
 *  =============
 *  1- Sarisssa  (http://sarissa.sourceforge.net/)
 *  2- Yahoo YUI (for cross-browser XMLHttpRequest Object functionality)
 *  
 */

/*
 *   docLoadedEvent: gets fired whenever there is an update on a document's loading status.
 *                   with code=true if success and code=false if a failure occured.
 *                   
 */ 
XMLclient = function( XMLsrcURL, XSLsrcURL, docStatusEvent, articlePath )
{
	this.XMLsrcURL  = XMLsrcURL;
	this.XSLsrcURL  = XSLsrcURL;
	this.targetEl   = null;
	this.eDocStatus = docStatusEvent;
	
	this.XMLdoc = null;
	this.XSLdoc = null;
	
	this.XMLloadingErr = null;
	this.XSLloadingErr = null;
	
	this.transformed = false;
	
	this.articlePath = articlePath;
};
XMLclient.prototype =
{
	handleXMLsuccess: function(o)
	{
		this.XMLdoc = o.responseXML;
		this.XMLloadingErr = false;
		this.eDocStatus.fire( {code:true } ); 
		
		// if both XML and XSL are available, transform!		
		if ((this.XMLdoc !== null) && (this.XSLdoc!== null))
			{ this.transform(); }
	},
	handleXMLfailure: function(o)
	{
		this.XMLloadingErr = true; 
		this.eDocStatus.fire( {code:false } );		
	},
	handleXSLsuccess: function(o)
	{
		this.XSLdoc = o.responseXML;
		this.XSLloadingErr = false; 
		this.eDocStatus.fire( {code:true } );
		
		// if both XML and XSL are available, transform!
		if ((this.XMLdoc !== null) && (this.XSLdoc !== null))
			{ this.transform(); }

	},
	handleXSLfailure: function(o)
	{
		this.XSLloadingErr = true; 
		this.eDocStatus.fire( {code:false } );		
	},
	
	transform: function()
	{
		// paranoia!
		if (this.transformed)
			{ return; }
			
		// Sarissa dependancy
		var proc = new XSLTProcessor();
		try{
			proc.importStylesheet(this.XSLdoc);
		} catch(e){ this.reportException("importStyleSheet", e); }
		
		try {
			Sarissa.updateContentFromNode( this.XMLdoc, this.targetEl, proc );
		} catch(e) { this.reportException("Sarissa.updateContentFromNode",e); }
		
		this.transformed = true;
	},

	/*
	 *   This method is used to initiate the XSLT transform
	 *   on the specified XML source document. 
	 *   This method is asynchronous and uses the callback methods
	 *   specified in the constructor class to return status.
	 */
	doprocess: function(el)	
	{
		this.targetEl = el;
		
		var XMLcb = {	success: this.handleXMLsuccess,
						failure: this.handleXMLfailure,
						scope: this	};
		var XSLcb = {	success: this.handleXSLsuccess,
						failure: this.handleXSLfailure,
						scope: this };
		
		// First, we need to fetch the XML & XSL data files
		var rxml = YAHOO.util.Connect.asyncRequest('GET', this.XMLsrcURL, XMLcb, null );
		
		var rxsl = YAHOO.util.Connect.asyncRequest('GET', this.XSLsrcURL, XSLcb, null );
	},
	
	reportException: function (s,e)
	{
		alert("Exception raised: " + e );	
	}
		
};
