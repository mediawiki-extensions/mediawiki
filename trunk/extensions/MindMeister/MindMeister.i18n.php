<?php
/**
 * @author Jean-Lou Dupont
 * @package MindMeister
 * @version @@package-version@@
 * @Id $Id$
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
// the format is important here too: 'msg'.$classname
MW_MindMeister::setMessages(
	array( 'en' => 
		array(
			'mindmeister'				=> 'MindMeister: ',
			'mindmeister-html'			=> '<iframe src="http://www.mindmeister.com/maps/public_map_shell/$1?width=$2&height=$3&zoom=$4" $5></iframe>', 
			'mindmeister-tpl-missing'	=> 'missing mandatory parameter <b>$1</b> ',
			'mindmeister-tpl-invalid'	=> 'invalid parameter <b>$1</b> ',
			'mindmeister-tpl-type'		=> 'parameter <b>$1</b> should be <i>$2</i> ',				
			#'' => '',
		),
			#other languages
			#'fr' =>
	)
);
//</source>