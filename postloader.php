<?php
/**
 * Postloader.php
 *
 * Extension allows postloading of custom content into targeted edit forms
 * when creating an article.
 *
 * The created page gets text from the <postload-head> section pre-pended and the
 * text <postload-tail> text appended to the edit form.
 * 
 * Also adds a new tag <nopostload> which is used to mark sections which
 * shouldn't be postloaded, ever; has no effect on the rendering of pages
 *
 * This extension has been adapted from the "preloader" extension
 * authored by Rob Church.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Jean-Lou Dupont - http://bluecortex.com
 *
 * HISTORY:
 * v1.0
 */
$postloaderVersion = "(v1.0)";

$wgExtensionCredits['other'][] = array(
    'name' => "Postloader $postloaderVersion [http://www.bluecortex.com]",
	'author' => 'Jean-Lou Dupont [http://www.bluecortex.com]' 
);

if( defined( 'MEDIAWIKI' ) ) {

    $wgExtensionFunctions[] = 'efPostloader';
    
    /**
     * Sources of postloaded content for each namespace
     *
     * e.g. (include in LocalSettings.php) 
     * $wgPostloaderSource[ NS_MAIN ] = 'Template:Postload';
     */
         
    function efPostloader() {
        new Postloader();
    }
    
    class Postloader {
    
        function Postloader() {
            $this->setHooks();
        }

        function mainHook( &$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags ) 
		{
			# First off, check if the article being saved is new
			if ( ! ($flags & EDIT_NEW) )
				return true;
			
			# Check if there is a source page to postload
            $src = $this->postloadSource( $article->mTitle->getNamespace() );
            if( $src ) 
			{
				$head='';
				$tail='';
				
                $stx = $this->sourceText( $src );
							
                if( $stx )
				{
					# Now that we have the page to postload, 
					# We need to extract the <head> and <tail> parts
					preg_match("/<postload-head>(.*?)<\/postload-head>/si",$stx, $head);
					preg_match("/<postload-tail>(.*?)<\/postload-tail>/si",$stx, $tail);

                    $text = $head[1].$text.$tail[1];
				}
            }
			
			return true;
        }
        
        /** Hook function for the parser */
        function parserHook( $input, $args, &$parser ) 
		{
            $output = $parser->parse( $input, $parser->mTitle, $parser->mOptions, false, false );
            return $output->getText();
        }
        
        /**
         * Determine what page should be used as the source of postloaded text
         * for a given namespace and return the title (in text form)
         *
         * @param $namespace Namespace to check for
         * @return mixed
         */ 
        function postloadSource( $namespace ) {
            global $wgPostloaderSource;
            if( isset( $wgPostloaderSource[ $namespace ] ) ) {
                return $wgPostloaderSource[ $namespace ];
            } else {
                return false;
            }
        }
        
        /**
         * Grab the current text of a given page if it exists
         *
         * @param $page Text form of the page title
         * @return mixed
         */
        function sourceText( $page ) {
            $title = Title::newFromText( $page );
            if( $title && $title->exists() ) {
                $revision = Revision::newFromTitle( $title );
                return $this->transform( $revision->getText() );
            } else {
                return false;
            }
        }
        
        /**
         * Remove <nopostload> sections from the text
         *
         * @param $text
         * @return string
         */
        function transform( $text ) {
            return preg_replace( '/<nopostload>.*<\/nopostload>/', '', $text );
        }
        
        /** Register the hook functions with MediaWiki */
        function setHooks() {
            global $wgHooks, $wgParser;
            $wgHooks['ArticleSave'][] = array( &$this, 'mainHook' );
            $wgParser->setHook( 'nopostload', array( &$this, 'parserHook' ) );
        }
    
    }

} else {
    echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
    die( 1 );
}
?>