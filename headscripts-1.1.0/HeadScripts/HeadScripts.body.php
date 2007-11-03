<?php
/**
 * @author Jean-Lou Dupont
 * @package HeadScripts
 * @version $Id$
 */
//<source lang=php>
class HeadScripts
{
	// 
	const thisType = 'other';
	const thisName = 'HeadScripts';
	const rpage = 'HeadScripts';

	// Javascript related
	const scBegin = '<script type="text/javascript" src="';
	const scEnd   = '"></script>';
	const cssBegin= '<link rel="stylesheet" type="text/css" href="';
	const cssEnd  = '" />';

	// TABLE FORMATTING related
	static $columnSeparator = "||";
	static $rowStart = "|";
	static $rowEnd   = "|-";

	/**
		{{#headscript: uri [| notes] }}
	 */
	public function mg_headscript( &$parser, $uri, $notes = null )
	{
		static $index = 0;
		
		$page = self::rpage;
		wfRunHooks( 'RegistryPageSet',	array( $page, $index++, 
											array( 'type' => 'js', 'uri' => $uri ) ));
		
		// Format a nice wikitext line
		return	self::$rowStart.
				'js'.self::$columnSeparator.	
				'script ['.$uri.']'.self::$columnSeparator.
				$notes."\r\n".
				self::$rowEnd."\r\n";
	}
	/**
		{{#headcss: uri [| notes] }}
	 */
	public function mg_headcss( &$parser, $uri, $notes = null )
	{
		static $index = 0;
		
		$page = self::rpage;
		wfRunHooks( 'RegistryPageSet',	array( $page, $index++, 
											array( 'type' => 'css', 'uri' => $uri ) ));
		
		// Format a nice wikitext line
		return	self::$rowStart.
				'css'.self::$columnSeparator.
				'['.$uri.']'.self::$columnSeparator.
				$notes."\r\n".
				self::$rowEnd."\r\n";
	}

	/**
	 *
	 */
	public function hBeforePageDisplay( &$op )
	{
		$params = null;
		$page = self::rpage;
		
		wfRunHooks( 'RegistryPageGet', array( $page, &$params) );
		
		if (empty( $params ) )
			return true;
		
		foreach( $params as &$e )
			switch( $e['type'])
			{
				case 'js': 
					$op->addScript( self::scBegin.$e['uri'].self::scEnd );
					break;
				case 'css': 
					$op->addScript( self::cssBegin.$e['uri'].self::cssEnd );
					break;
			}

		
		return true;
	}

	public function hSpecialVersionExtensionTypes( &$sp, &$extensionTypes )
	// setup of this hook occurs in 'ExtensionClass' base class.
	{
		global $wgExtensionCredits;

		$result1 =  "[http://mediawiki.org/wiki/Extension:RegistryManager RegistryManager] is ";
		$result1 .= (class_exists('RegistryManager')) ? 'present.':'absent.';
		$result1 .= ' Configuration registry page available [[MediaWiki:Registry/'.self::rpage.']].';
		
		foreach ( $wgExtensionCredits[self::thisType] as $index => &$el )
			if (isset($el['name']))		
				if ($el['name'] == self::thisName)
					$el['description'] .= $result1.'<br/>';
				
		return true; // continue hook-chain.
	}	
}

//</source>
//</source>