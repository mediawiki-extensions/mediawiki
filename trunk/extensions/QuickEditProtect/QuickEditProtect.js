/**
 * QuickEditProtect.js
 * 
 * @author Jean-Lou Dupont
 * @version @@package-version@@
 * @id $Id$
 */

// Keep it simple: just declare an object
// which is somewhat analoguous to a "singleton" pattern 
QuickEditProtect = {
	
	inprogress:false,
	timeoutID:null,
	tabID:null,
	
};
/**
 * Performs AJAX call
 */
QuickEditProtect.ajaxCall = function( fnc ) {
	
	if (QuickEditProtect.inprogress) {
		return false;
	}

	QuickEditProtect.inprogress = true;
	
	sajax_do_call(
		"MW_QuickEditProtect::toggle",
		[wgPageName], 
		QuickEditProtect.processResult
	);
	
	// if the request isn't done in 10 seconds, allow user to try again
	QuickEditProtect.timeoutID = window.setTimeout(
		function() { QuickEditProtect.inprogress = false; },
		10000
	);
	return false;
}
/**
 * Process callback response 
 */
QuickEditProtect.processResult = function(request) {

	var response;
	var raw_response = request.responseText;
	
	QuickEditProtect.inprogress = false;

	// expecting JSON response
	try {
		eval( 'response=' + raw_response );
	} catch(e) {
		response = null;
	}
	
	// paranoia
	if ( QuickEditProtect.tabID && (response != null))
		//wikibits.js
		changeText(QuickEditProtect.tabID.firstChild, response.text );
	
	if(QuickEditProtect.timeoutID) {
		window.clearTimeout(QuickEditProtect.timeoutID);
	}
	
	return;
}

/**
 * onLoad method
 */
QuickEditProtect.onLoad = function() {
	
	// only one of these at one time
	QuickEditProtect.tabID = document.getElementById( 'ca-quickeditprotect' );	

	if ( QuickEditProtect.tabID )
		QuickEditProtect.tabID.firstChild.onclick = QuickEditProtect.ajaxCall;
}

hookEvent( "load", QuickEditProtect.onLoad );