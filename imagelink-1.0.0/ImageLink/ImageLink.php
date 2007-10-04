<?php
/**
 * @author Jean-Lou Dupont
 * @package ImageLink
 */
//<source lang=php>*/
StubManager::createStub(	'ImageLink', 
							dirname(__FILE__).'/ImageLink.body.php',
							null,					// i18n file			
							array('ParserAfterTidy'),	// hooks
							false, 					// no need for logging support
							null,					// tags
							array('imagelink'),	// parser Functions
							null
						 );
//</source>