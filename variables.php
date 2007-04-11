<?php
/*
 * Variables.php
 *
 * Original author: Rob Adams
 * Modifications:   Jean-Lou Dupont
 *
 * NOTE: when passing a variable name through the <varset> and
 *       <varget> interface, make sure only to use lowercase.
 *       This is a Mediawiki imposed limitations.
 * 
 * History:
 *
 * V1.1 (JLD)   
 *  - Added Wikitext interface through custom <varget> and <varset> tags
 *  - Added direct PHP interface.
*/
if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['parserhook'][] = array( 'name' => 'VariablesExtension',  
                                             'author' => 'Rob Adams, Jean-Lou Dupont' );

$wgExtensionFunctions[] = 'wfSetupVariables';
$wgHooks['LanguageGetMagic'][]  = 'wfVariablesLanguageGetMagic';

class ExtVariables {
    var $mVariables= array();

	/**************************************************************************
	*  Magic Word wikitext interface
	*/

    function vardefine( &$parser, $expr = '', $value = '' ) 
	{
        $this->mVariables[$expr] = $value;
        return '';
    }

    function varf( &$parser, $expr = '' ) { return $this->mVariables[$expr]; }
	
	/**************************************************************************
	 * Direct interface from PHP script 
	*/
	
    function getvar( $expr = '' )         { return $this->mVariables[$expr];   }
    function setvar( $expr, $value )      { $this->mVariables[$expr] = $value; }

	/***************************************************************************
	*  Wikitext interface 
	*/
	function vartag_set ( $input, $argv, &$parser )
	/*
	*  e.g. <varset varname=value/>
	*/
	{
		$this->mVariables = array_merge( $this->mVariables, $argv );
		return '';
	}
	function vartag_get( $input, $argv, &$parser ) 
 	/*
	*    e.g.  <varget varname/>
	*/
	{
		$vars = array_keys( $argv );
		return $this->mVariables[ $vars[0] ];	
	}
}

function wfSetupVariables() 
{
    global $wgExtVariables;
	global $wgParser;

    $wgExtVariables = new ExtVariables;

	// Magic Word Wikitext interface
    $wgParser->setFunctionHook( 'vardefine', array( &$wgExtVariables, 'vardefine' ) );
    $wgParser->setFunctionHook( 'var', array( &$wgExtVariables, 'varf' ) );

	// Wikitext interface
	$wgParser->setHook( 'varset', array( &$wgExtVariables, 'vartag_set' ) );
	$wgParser->setHook( 'varget', array( &$wgExtVariables, 'vartag_get' ) );
}

function wfVariablesLanguageGetMagic( &$magicWords, $langCode ) 
{
	$magicWords['vardefine'] = array( 0, 'vardefine' );
	$magicWords['var'] = array( 0, 'var' );
	return true;
}
?>