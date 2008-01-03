<?php
/**
 * @author Jean-Lou Dupont
 * @package HeaderFooter
 * @version @@package-version@@
 * @Id $Id$
 */

class HeaderFooter
{
	public function hParserBeforeStrip( &$parser, &$text, &$mStripState )
	{
		global $wgTitle;
		
		$thisTitle     = $parser->getTitle();
		$thisTitleNs   = null;
		$thisTitleName = null;

		if (is_object( $thisTitle ) && ( $thisTitle instanceof Title) )
		{
			$thisTitleNs   = $thisTitle->getNamespace();
			$thisTitleName = $thisTitle->getPrefixedDBKey();
		}
		$ns = $wgTitle->getNsText();
		$name = $wgTitle->getPrefixedDBKey();
		$protect = $wgTitle->isProtected( 'edit' );
	
		// make sure we are only including the headers/footers to the main article!
		if (( $thisTitleNs === $ns) && ($thisTitleName === $name ) )
			return true;
		
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