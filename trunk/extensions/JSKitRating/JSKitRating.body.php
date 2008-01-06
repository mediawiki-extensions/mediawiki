<?php
/**
 * @author Jean-Lou Dupont
 * @package JSKitRating
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>
class JSKitRating
{
	const thisType = 'other';
	const thisName = 'JSKitRating';
	/**
	 * Parameters:
	 * imageurl
	 * imagesize
	 * path
	 * starColor
	 * view
	 */
	public function mg_jskitrating( &$parser )
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
