<?php
/**
 * @author Jean-Lou Dupont
 * @package HookPack
 * @category Enhancements
 * @version @@package-version@@
 * @Id $Id$
 */
//<source lang=php>

require 'aop/aop.php';

// add Mediawiki's includes directory
aop::register_class_pointcut_definition( 'Linker', dirname(__FILE__) . '/hooks/Linker.pointcut.definition.php' );
aop::register_class_path( $IP.'/includes' );
aop::register_class_bypass( "SpecialPage" );
aop::register_class_bypass( "EditPage" );

$logger = &Log::singleton('file', dirname(__FILE__).'/log.txt', '' );

aop::setDebug();
aop::setLogger( $logger );

//</source>