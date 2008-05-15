<?php
/**
 * @package SecureWidgets
 * @category Widgets
 * @author Jean-Lou Dupont
 * @version @@package-version@@ 
 * @Id $Id$
 */

MW_WidgetRenderer::getInstance('MW_WidgetRenderer')->setMessages(
	array( 'en' => 
		array(
			'securewidgets-renderer'				  => 'SecureWidgets - renderer',		
			'securewidgets-renderer-missing-inputs'   => 'missing input variables for widget <i>$1</i>.',
			'securewidgets-renderer-missing-input'    => 'missing input parameter <i>$1</i> of type <i>$2</i>',
			'securewidgets-renderer-unsupported-type' => 'unsupported parameter type <i>$2</i> for parameter <i>$1</i>',		
			'securewidgets-renderer-type-mismatch'    => 'parameter type mismatch: expecting type <i>$2</i> for parameter <i>$1</i>',		
			#'' => '',
		),
			#other languages
			#'fr' =>
	)
);
