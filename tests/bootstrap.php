<?php

if ( PHP_SAPI !== 'cli' ) {
	die( 'Not an entry point' );
}

if ( !is_readable( $autoloaderClassPath = __DIR__ . '/../../SemanticMediaWiki/tests/autoloader.php' ) ) {
	die( 'The SemanticMediaWiki test autoloader is not available' );
}

print sprintf( "\n%-20s%s\n", "Semantic Forms Select: ", SFS_VERSION );
print sprintf( "%-20s%s\n", "Semantic Forms: ", defined( 'SF_VERSION' ) ? SF_VERSION : 'undefined' );

$autoloader = require $autoloaderClassPath;
$autoloader->addPsr4( 'SFS\\Tests\\', __DIR__ . '/phpunit/Unit' );
$autoloader->addPsr4( 'SFS\\Tests\\Integration\\', __DIR__ . '/phpunit/Integration' );
