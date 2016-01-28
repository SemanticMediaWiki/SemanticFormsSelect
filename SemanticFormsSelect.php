<?php

/**
 * @see https://github.com/SemanticMediaWiki/SemanticFormsSelect/
 * @link https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect
 *
 * @defgroup SFS SemanticFormsSelect
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of the SemanticFormsSelect extension, it is not a valid entry point.' );
}

if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.23', 'lt' ) ) {
	die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">SemanticFormsSelect</a> is only compatible with MediaWiki 1.23 or above. You need to upgrade MediaWiki first.' );
}

$GLOBALS['wgExtensionFunctions'][] = function() {
	if ( version_compare( 'SF_VERSION', '2.8', '<' ) ) {
		die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">SemanticFormsSelect</a> is only compatible with Semantic Forms 2.8 or above. You need to upgrade <a href="https://www.mediawiki.org/wiki/Extension:Semantic_Forms">Semantic Forms</a> first.' );
	}
};

// Do not initialize more than once.
if ( defined( 'SFS_VERSION' ) ) {
	return 1;
}

define( 'SFS_VERSION', '1.3.0' );

/**
 * @codeCoverageIgnore
 */
call_user_func( function() {

	$GLOBALS['wgExtensionCredits']['semantic'][] = array(
		'path' => __FILE__,
		'name' => 'Semantic Forms Select',
		'author' =>array( 'Jason Zhang', 'Toni Hermoso Pulido', '...' ),
		'url' => 'https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect',
		'description' => 'Allows to generate a select field in a semantic form whose values are retrieved from a query',
		'version'  => SFS_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	// Api modules
	$GLOBALS['wgAPIModules']['sformsselect'] = 'SFS\ApiSemanticFormsSelect';

	$GLOBALS['wgScriptSelectCount'] = 0;
	$GLOBALS['wgSF_Select_debug'] = 0;

	// Register resource files
	$extensionPathParts = explode( DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR , __DIR__, 2 );

	$GLOBALS['wgResourceModules']['ext.sf_select.scriptselect'] = array(
		'localBasePath' => __DIR__ ,
		'remoteExtPath' => end( $extensionPathParts ),
		'position' => 'bottom',
		'scripts' => array( 'res/scriptSelect.js' ),
		'dependencies' => array(
			'ext.semanticforms.main'
		)
	);

	$GLOBALS['wgExtensionFunctions'][] = function() {
		$GLOBALS['sfgFormPrinter']->setInputTypeHook( 'SF_Select', '\SFS\SemanticFormsSelect::init', array() );
	};

} );
