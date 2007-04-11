<?php
# Runphp_page 
# Mediawiki Extension
#
# CAUTION:
# ========
# ONLY USE THIS IN AN TRUSTED ENVIRONMENT 
# 
# This extension allows you to run PHP-Code loaded from a Wiki-Article inside a 
# Wiki-Articles using the following syntax:
# <runphp [optional arguments]> code here </runphp>
# <runphppage [optional arguments]> page title </runphppage>
#
# The executable code can either be the whole page itself OR
# just the section enclosed within the <php> PHP code here </php>.
# This feature is to allow for integration with GeSHi syntax highlighting.
#
# Furthermore, conditional execution of a PHP page is supported through the
# following syntax:
# {{#runphp:page|condition [|optional arguments]}}
# 
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/runphp_page.php");
# Author: Jean-Lou Dupont - http://bluecortex.com
#
# HISTORY
# V1.1
#  - Added interface to load & execute PHP based MW page
#    from outside objects.
#  - Remove useless $parser parameter from execPage
# v1.2
#  - Added support for empty code page detection 
#    (nicer integration with other extensions)
#
# v1.3
#  - Added capability to pass arguments
#  - Added some checks for non existing pages.
#
# v1.4
#  - Added support for silently ignoring options
#    within the <php options here> tag </php> 
#
# v1.5
#  - Added support for composite page
#    i.e. executing a page that contains wikitext 
#    and executable <php> code.
#    The page, when parsed, obviously also
#    gets <runphp> enclosed tags executed.
#
# v1.6
#  - Added security feature: only pages with 'sysop edit restriction'
#    can execute PHP code.
#
# USAGE NOTE:
#  When arguments are passed with <runphp> and <runphppage>,
#  then a callback function must be used. It is not possible
#  (to the author's knowledge that is) to pass arguments
#  to PHP's 'eval' function. Thus, use the following trick
#  in the code/page:
# 
#  <php> #integration with GeSHi
#    function callback( $args )
#    {  do some stuff here }
#    return "callback";
#  </php>
#
# COMPATIBILITY:
#  - Tested on Mediawiki v1.8.2
#
$wgExtensionCredits['other'][] = array( 
	'version'=> "1.6",
    'name'   => "RunPHP Page [http://www.bluecortex.com]",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

$wgExtensionFunctions[]        = 'RunPHPsetup';
$wgHooks['LanguageGetMagic'][] = 'mgRunPHP';

function RunPHPsetup()
{
  global $wgParser;

  # Basic RunPHP
  # E.g. <runphp [optional arguments]> inline code here </runphp>
  $wgParser->setHook( "runphp", "parsePHP" );	
	
  # RunPHP page
  # E.g. <runphppage [optional arguments]> page title </runphppage>
  $wgParser->setHook( "runphppage", "parsePHP_page" );
	
  # Magic Word based RunPHP
  # E.g. {{runphp:page title|condition}}
  $wgParser->setFunctionHook( 'runphp', 'MagicRunPHP' );
}
#
# Basic RunPHP
# E.g. <runphp [optional arguments]> inline code here </runphp>
function parsePHP( $input, $argv, &$parser ) { return runphpExecCode( $input, $argv ); }
#
# RunPHP page
# E.g. <runphppage [optional arguments]> page title </runphppage>
function parsePHP_page( $input, $argv, &$parser ) {  return runphpExecPage( $input, $argv ); }
#
# Magic Word based RunPHP
#
# {{#runphp: page name | condition }}
# if (condition === true) then execute 
#
function MagicRunPHP( &$parser, $page = '', $cond = true ) 
{
	if ($cond)
	{
	  $args = func_get_args();
	  #just leave the intended parameters
	  array_shift( $args ); # $parser
	  array_shift( $args ); # $page
	  array_shift( $args ); # $cond
	  return runphpExecPage ( $page, $args );
	}
	else 
		return '';
}

function mgRunPHP( &$magicWords, $langCode ) 
{
	$magicWords['runphp'] = array( 0, 'runphp' );
    return true;
}

#
# ExecPage function
# 
# NOTE: Arguments MUST be passed as an array.
#
function runphpExecPage( $p, $argv=null ) { return runphpExecCode( runphpGetCode( $p ) , $argv ); }

/*
 * This function fetches the PHP code for a MW page.
 * The code can be optionally wrapped in <php></php> tags
 * used for GeSHi integration.
 *
 * If node code is present on the page or worse the page
 * does not exists, 'null' is returned.
*/
function runphpGetCode( $page_title )
{
  $page_content = runphpGetPageContent( $page_title );

  return runphpExtractCode( $page_content );
}

function runphpExtractCode( $page_content )
{
  # Integration with GeSHi
  # The PHP page can be highlighted with GeSHi whilst
  # still being executable.
  # new pattern as of Version 1.4
  $r = preg_match( "/<php(?:.*)\>(.*)(?:\<.?php>)/siU", $page_content,  $c );
  
  # Only one code block is allowed per page.
  if ($r==1)
    $code = $c[1];  # page is GeSHi highlighted. 
  else
    $code = $page_content;  # straight PHP code stored on page.
 
  return $code;
}

function runphpExecCode( $code, $argv=null )
{
  // v1.6 security feature
  global $wgTitle;
  $proceed = runphpCheckRight( $wgTitle );
  if (! $proceed )
	return "unauthorized <b>runphp</b> usage.";

  # start capturing the user code's output
  ob_start();
	
  # can't pass arguments directly with 'eval'
  # must load the code in the PHP interpreter and
  # get a callback function name returned.
  // NOTE: 'eval' does not mind being passed 
  // a 'null' parameter
  $callback = eval( $code );
  
  # look for arguments.
  if ( count($argv)>0 )
  	call_user_func( $callback, $argv );

  $output = ob_get_contents();
  
  ob_end_clean();

  return $output;
}
/*
 * New function in version 1.5
 * BUT just a refacturing of existing
 * functionality to better support
 * composite page execution.
*/
function runphpGetPageContent( $p )
{
  global $mediaWiki;
	
  $title = Title::newFromText( $p );
  
  if ($title == null)
   return null;
  
  $article = $mediaWiki->articleFromTitle($title);
	
  if ($article == null )
    return null;
	
  $article->loadContent();

  # if no page or an empty one
  if (!$article->mDataLoaded)
  	return null;
	 
  return $article->mContent;
}

/*
 * Function to execute a composite page
 * (new in v1.5)
 *
 * FUNCTIONALITY:
 * ==============
 * A) When the referred page is "read" (as per standard MW behavior) then:
 *   1) Page is parsed as normal
 *   2) Any <runphp> sections are executed (assuming runphp extension is available)
 *   3) Any <php> section is handled as per default (either Wikitext or any other 
 *      parser extension modifying this behavior).
 * B) When the page is referred for "execution" using this function then:
 *   1) Tags <php> are transformed to <runphp> ones
 *   2) Page content is then parsed as usual
 *
*/
function runphpExecPageEx( $page_title ) 
{
	// Get the argument(s) (if any)
	// Get rid of the page_title parameter passed 
	// to this function BECAUSE the behavior differs
	// if we pass some arguments to runphpExecCode.
	$args = func_get_args();
	$args = array_shift( $args );

	if (empty($args)) $args = null;

	// First, let's get the page content
	$page_content = runphpGetPageContent( $page_title );
	 
	// if we get no page content, get out before
	// we start breaking other things
	if ( $page_content == null) return;
	 
	// Next, determine if the page contains the
	// meta tag denoting a "composite page"
	// In this version, the tag is not very sophisticated.
	$pattern = "/<composite(.?)>/";
	$result = preg_match( $pattern, $page_content);
	
	// get rid of this meta tag
	$page_content = preg_replace( $pattern, "", $page_content);

	// If we are faced with a "standard" page, then
	// use the "standard" function.
	if (!$result)
		return runphpExecCode( $code, $args );

	// From this point, we are assuming we have a composite page
	// on our hands.
	// - Turn the <php> tags in <runphp> ones
	// - Parse the page content
	$pattern1 = "/\/php>/siU";
	$replace1 = "/runphp>";
	$pattern2 = "/\<php/siU";
	$replace2 = "<runphp";
	
	$result1  = preg_replace($pattern1, $replace1, $page_content);
	$result   = preg_replace($pattern2, $replace2, $result1);

	#$parser = new Parser;
 	#$output=$parser->recursiveTagParse($result);
	
	$output=$result;
	return $output;	
}

function runphpCheckRight( $title )
// v1.6 security feature.
{
  $proceed = false;
  
  $state = $title->getRestrictions('edit');
  foreach ($state as $index => $group )
  	if ( $group == 'sysop' )
		$proceed = true;

  return $proceed;		
}

?>