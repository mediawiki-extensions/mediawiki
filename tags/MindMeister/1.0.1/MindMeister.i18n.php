<?php
/**
 * @author Jean-Lou Dupont
 * @package MindMeister
 * @version 1.0.1
 * @Id $Id: MindMeister.i18n.php 1053 2008-04-30 01:12:01Z jeanlou.dupont $
 */
//<source lang=php>

/*
 * <iframe	width="600" 
 * 			height="400" 
 * 			frameborder="0" 
 *			scrolling="no" 
 * 			style="overflow:hidden" 
 * 			src="http://www.mindmeister.com/maps/public_map_shell/6080704?width=600&height=400&zoom=1">
 * </iframe>
 * 
 * 
 */

MW_MindMeister::getInstance('MW_MindMeister')->setMessages(
	array( 'en' => 
		array(
			'mindmeister'				=> 'MindMeister: ',
			'mindmeister-help'			=> "\n\n".'<pre><div class="error-help">MindMeister Help: {{#mindmeiser: mmid=id [|mm_width=] [|mm_height=] [|mm_zoom=] [|width=] [|height=] [|class=] [|frameborder=] [|scrolling=] [|style=] [|id=] }}</div></pre>',
			'mindmeister-html'			=> '<iframe src="http://www.mindmeister.com/maps/public_map_shell/$1" $2></iframe>',
			'mindmeister-example'		=> '<br/><html><div class="error-example"><iframe width="600" height="400" frameborder="0" src="http://www.mindmeister.com/maps/public_map_shell/6089041?width=600&height=600&zoom=1" scrolling="no" style="overflow:hidden"></iframe></div></html>', 
			'mindmeister-tpl-missing'	=> 'missing mandatory parameter <b>$1</b>',
			'mindmeister-tpl-invalid'	=> 'invalid parameter <b>$1</b>',
			'mindmeister-tpl-type'		=> 'parameter <b>$1</b> should be <i>$2</i>',				
			#'' => '',
		),
			#other languages
			#'fr' =>
	)
);
//</source>