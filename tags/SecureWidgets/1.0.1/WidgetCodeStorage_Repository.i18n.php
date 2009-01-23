<?php
/**
 * @author Jean-Lou Dupont
 * @package SecureWidgets
 * @version 1.0.1
 * @Id $Id: WidgetCodeStorage_Repository.i18n.php 1109 2008-05-15 19:29:49Z jeanlou.dupont $
 */
//<source lang=php>

MW_WidgetCodeStorage_Repository::getInstance('MW_WidgetCodeStorage_Repository')->setMessages(
	array( 'en' => 
		array(
			'securewidgets-csrepo'				=> 'SecureWidgets - SVN Repository',
			'securewidgets-csrepo-not-found'	=> 'code not in the SVN repository.',
			'securewidgets-csrepo-error-feed'	=> 'error fetching widget feed list.',
			'securewidgets-csrepo-widget-not-found' => "widget by the name <i>$1</i> was not found in the SVN repository.",		
			'securewidgets-csrepo-error-code-fetch' => "error fetching code for widget <i>$1</i> from SVN repository.",		
			#'' => '',
		),
			#other languages
			#'fr' =>
	)
);
//</source>