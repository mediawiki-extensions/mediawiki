<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitRating
 * @version 1.0.0
 * @Id $Id$
 */
//<source lang=php>
class JSKitRating
{
	const thisType = 'other';
	const thisName = 'JSKitRating';
	
	public function mw_JSKITRATING( &$parser, &$varcache, &$ret )
	{
		global $wgTitle;
		
		$title = $wgTitle->getPrefixedDBkey();
		
		$output = <<<EOT
<div class="js-kit-rating" title="$title" permalink="$title"></div>
<script src="http://js-kit.com/ratings.js"></script>
EOT;

		return array( $output, 'noparse' => true, 'isHTML' => true );
	}

} // end class
//</source>
