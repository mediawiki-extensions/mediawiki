<?php
/**
 * @author Jean-Lou Dupont
 * @package AddScriptCss
 */
//<source lang=php>*/
StubManager::createStub(	'AddScriptCss', 
							dirname(__FILE__).'/AddScriptCss.body.php',
							null,							
							array( 'OutputPageBeforeHTML', 'ParserAfterTidy' ),
							false, 								// no need for logging support
							array( 'addtohead', 'addscript' ),	// tags
							array( 'addscript' ), 				//of parser function magic words,
							null
						 );
//</source>