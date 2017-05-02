<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL | E_STRICT );
date_default_timezone_set( 'UTC' );
ini_set( 'display_errors', 1 );

global $IP;

//if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
if ( !is_readable( $autoloaderClassPath = $IP . '/extensions/SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( "\nThe Semantic MediaWiki test autoloader is not available\n" );
}

// if ( !class_exists( 'SemanticFormsSelect' ) || ( $version = SemanticFormsSelect::getVersion() ) === null ) {
if ( ExtensionRegistry::getInstance()->isLoaded( 'SemanticFormsSelect' ) ) {
	die( "\nSemantic Forms Select is not available, please check your Composer or LocalSettings.\n" );
}

//print sprintf( "\n%-20s%s\n", "Semantic Forms Select: ", ExtensionRegistry::getInstance()->isLoaded( 'SemanticFormsSelect' ) );
//print sprintf( "%-20s%s\n", "Page Forms: ", SemanticFormsSelect::getVersion( 'PageForms' ) );

$autoloader = require $autoloaderClassPath;
//$autoloader->addPsr4( 'SFS\\Tests\\', __DIR__ . '/phpunit/Unit' );
//$autoloader->addPsr4( 'SFS\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
unset( $autoloader );
