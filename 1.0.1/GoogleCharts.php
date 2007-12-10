<?php
/**
 * @author Jean-Lou Dupont
 * @package GoogleCharts
 * @version 1.0.1
 * @Id $Id: GoogleCharts.php 731 2007-12-10 01:20:52Z jeanlou.dupont $
*/
//<source lang=php>
if ( class_exists('StubManager') )
{
	$wgExtensionCredits['other'][] = array( 
		'name'    	=> 'GoogleCharts',
		'version' 	=> '1.0.1',
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