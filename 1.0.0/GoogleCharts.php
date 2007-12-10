<?php
/**
 * @author Jean-Lou Dupont
 * @package GoogleCharts
 * @version 1.0.0
 * @Id $Id$
*/
//<source lang=php>
if ( class_exists('StubManager') )
{
	$wgExtensionCredits['other'][] = array( 
		'name'    	=> 'GoogleCharts',
		'version' 	=> '1.0.0',
		'author'  	=> 'Jean-Lou Dupont',
		'description' => "", 
		'url' 		=> 'http://mediawiki.org/wiki/Extension:GoogleCharts',	
	);
	StubManager::createStub2(	array(	'class' 		=> 'GoogleCharts', 
										'classfilename'	=> dirname(__FILE__).'/GoogleCharts.body.php',
										'hooks'			=> array( 'ParserAfterTidy' ),
										'mgs'			=> array( 'gcharts', 'gcharts_senc', 'gcharts_pipe' )
									)
							);
}						
//</source>