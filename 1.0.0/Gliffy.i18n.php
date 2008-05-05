<?php
/**
 * @author Jean-Lou Dupont
 * @package Gliffy
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>

/*
 <script src="http://www.gliffy.com/diagramEmbed.js" type="text/javascript"> </script>
 <script type="text/javascript"> gliffy_did = "1422012"; embedGliffy(); </script>
 */

MW_Gliffy::getInstance('MW_Gliffy')->setMessages(
	array( 'en' => 
		array(
			'gliffy'				=> 'Gliffy: ',
			'gliffy-help'			=> "\n\n".'<pre><div class="error-help">Gliffy Help: <nowiki>{{#gliffy: did=diagram-id}}</nowiki></div></pre>',
			'gliffy-html'			=> '<script src="http://www.gliffy.com/diagramEmbed.js" type="text/javascript"> </script><script type="text/javascript"> gliffy_did = "$1"; embedGliffy(); </script>',
			'gliffy-example'		=> '<br/><html><div class="error-example"><script src="http://www.gliffy.com/diagramEmbed.js" type="text/javascript"> </script><script type="text/javascript"> gliffy_did = "1422012"; embedGliffy(); </script></div></html>', 
			'gliffy-tpl-missing'	=> 'missing mandatory parameter <b>$1</b>',
			'gliffy-tpl-invalid'	=> 'invalid parameter <b>$1</b>',
			'gliffy-tpl-type'		=> 'parameter <b>$1</b> should be <i>$2</i>',				
			#'' => '',
		),
			#other languages
			#'fr' =>
	)
);
//</source>