<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

error_reporting( E_ALL | E_STRICT );
date_default_timezone_set( 'UTC' );
ini_set( 'display_errors', 1 );

global $IP;

if ( !is_readable(
	$autoloaderClassPath = $IP . '/extensions/SemanticMediaWiki/tests/autoloader.php'
) ) {
	die( "\nThe Semantic MediaWiki test autoloader is not available\n" );
}

if ( ExtensionRegistry::getInstance()->isLoaded( 'SemanticFormsSelect' ) ) {
	die( "\nSemantic Forms Select is not available, please check your Composer or LocalSettings.\n" );
}

$autoloader = require $autoloaderClassPath;
unset( $autoloader );
