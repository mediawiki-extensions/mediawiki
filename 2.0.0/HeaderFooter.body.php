<?php
/**
 * @author Jean-Lou Dupont
 * @package HeaderFooter
 * @version 2.0.0
 * @Id $Id$
 */

class HeaderFooter
{
	public function hParserBeforeStrip( &$parser, &$text, &$mStripState )
	{
		global $wgTitle;
		
		$ns = $wgTitle->getNsText();
		$name = $wgTitle->getPrefixedDBKey();
		$protect = $wgTitle->isProtected( 'edit' );
		
		$nsheader = wfMsg( "hf-nsheader-$ns" );
		$nsfooter = wfMsg( "hf-nsfooter-$ns" );		

		$header = wfMsg( "hf-header-$name" );
		$footer = wfMsg( "hf-footer-$name" );		

		$text = '<div class="hf-header">'.$this->conditionalInclude( '__NOHEADER__', $header, $protect ).'</div>'.$text;
		$text = '<div class="hf-nsheader">'.$this->conditionalInclude( '__NONSHEADER__', $nsheader, $protect ).'</div>'.$text;

		$text .= '<div class="hf-footer">'.$this->conditionalInclude( '__NOFOOTER__', $header, $protect ).'</div>';
		$text .= '<div class="hf-nsfooter">'.$this->conditionalInclude( '__NONSFOOTER__', $nsheader, $protect ).'</div>';
				
		return true;
	}

	protected function conditionalInclude( $disableWord, &$content, $protect )
	{
		// don't need to bother if there is no content.
		if (empty( $content ))
			return null;
		
		// is there a disable command lurking around?
		$disable = strpos( $content, $disableWord ) !== false ;
		
		// if there is, get rid of it
		// make sure that the disableWord does not break the REGEX below!
		$content = preg_replace('/'.$disableWord.'/si', '', $content );

		// if there is a disable command, then obey IFF the page is protected on 'edit'
		if ($disable && $protect)
			return null;
		
		return $content;
	}
		
} // END CLASS DEFINITION
?>