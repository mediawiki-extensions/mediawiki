/**
 * Clipboard.js 
 * $Id$
 * $LastChangedRevision$
 * version 1.0
 * 
 * This program is meant to be used in conjunction with
 * the Mediawiki 'Clipboard' extension.
 * 
 * Mediawiki Extension
 * @author Jean-Lou Dupont
 * http://www.bluecortex.com
 * 
 * Functions 'createCookie', 'readCookie' and 'eraseCookie'
 * are from: http://www.quirksmode.org/js/cookies.html
 * 
 */


mwClipboardClass = function( name )
{	this.currentPageName = name; }

mwClipboardClass.prototype =
{
	_CookieName:     'Clipboard',
	_TitleDelimiter: '~',
	_EditAreaList: [ 'wpTextbox1',  // from 'EditPage' when performing edition on an article
					 'pages',		// from 'Special:Export'
	 				],

	add: function()
	/*
	 *  Adds the current title to the Clipboard
	 */
	{
		c = this.readCookie( this._CookieName );
		if (c==null) c = '';
		c += this.currentPageName+this._TitleDelimiter;
		this.createCookie( this._CookieName, c );				
	},
	
	paste: function()
	{
		// first, find out which sort of <textarea> there is on this page
		r = this.findTextArea();
		if (r == undefined ) return;
		
		titles = this.readCookie( this._CookieName );
		
		// the browser's cookie functionality does not like
		// newline characters; that's why we needed an harmless
		// string delineation character and now we need to replace those.
		titles = titles.replace(/~/g,"\r");
		
		r.value += titles;
	},
	
	empty: function()
	/*
	 *   Erases the content of the Clipboard
	 */
	{ this.eraseCookie( this._CookieName );	},

	show: function()
	{
		titles = this.readCookie( this._CookieName );
		if (titles == null) 
			titles = "no titles in the clipboard";
		else
			titles = titles.replace(/~/g,"\n");
		
		alert( titles );		
	},

	// **************************************
	findTextArea: function()
	{
		for (i=0;i<this._EditAreaList.length;i++)
		{
			r = document.getElementsByName( this._EditAreaList[i] );
			if ( r[0] != undefined )
				break;
		}

		return r[0];
	},

	// **************************************
	createCookie: function (name, value, days) 
	{
		if (days) 
		{
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	},
	
	readCookie: function(name) 
	{
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) 
		{
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	},

	eraseCookie: function (name) 
	{ this.createCookie(name,"", -1); }
	
};

// the global variable 'wgPageName' is set by Mediawiki 
var mwClipboard = new mwClipboardClass( wgPageName );