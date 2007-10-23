<?php
/**
 * @author Jean-Lou Dupont
 * @package InterWikiTitleWizard
 * @version $Id$ 
 */
//<source lang=php>
$wgExtensionCredits['other'][] = array( 
	'name'        	=> 'InterWikiTitleWizard', 
	'version'     	=> '1.0.0',
	'author'      	=> 'Jean-Lou Dupont', 
	'description' 	=> 'Provides enhanced flexibility for inter-wiki titles',
	'url' 			=> 'http://mediawiki.org/wiki/Extension:InterWikiTitleWizard',			
);
if (class_exists('StubManager'))
	StubManager::createStub(	'InterWikiTitleWizard', 
								dirname(__FILE__).'/InterWikiTitleWizard.body.php',
								null,						// i18n file			
								array('GetFullURL'),	// hooks
								false, 						// no need for logging support
								null,						// tags
								null,
								null
							 );
else
	echo '[[Extension:InterWikiTitleWizard]] requires [[Extension:StubManager]].';							
//</source>