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

if ( !defined( 'SF_VERSION' ) || version_compare( SF_VERSION, '2.8', 'lt' ) ) {
   die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">SemanticFormsSelect</a> is only compatible with Semantic Forms 2.8 or above. You need to upgrade <a href="https://www.mediawiki.org/wiki/Extension:Semantic_Forms">Semantic Forms</a> first.' );
}

// Do not initialize more than once.
if ( defined( 'SFS_VERSION' ) ) {
	return 1;
}

define( 'SFS_VERSION', '1.3.0-alpha' );

//self executing anonymous function to prevent global scope assumptions
call_user_func( function() {

	$GLOBALS['wgExtensionCredits'][defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'specialpage'][] = array(
		'path' => __FILE__,
		'name' => 'Semantic Forms Select',
		'author' =>array( 'Jason Zhang', 'Toni Hermoso Pulido', '...' ),
		'url' => 'https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect',
		'description' => 'Allows to generate a select field in a semantic form whose values are retrieved from a query',
		'version'  => SFS_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	//$wgAjaxExportList[] = "QueryExecution";
	//$wgAjaxExportList[] = "FunctionExecution";
	$GLOBALS['wgExtensionFunctions'][] = function() {
		$GLOBALS['sfgFormPrinter']->setInputTypeHook( 'SF_Select', '\SFS\SemanticFormsSelect::init', array() );
	};

	$GLOBALS['wgAutoloadClasses']['SFS\SemanticFormsSelect'] = __DIR__ . '/src/SemanticFormsSelect.php';
	$GLOBALS['wgAutoloadClasses']['SFS\ApiSemanticFormsSelect'] = __DIR__ . '/src/ApiSemanticFormsSelect.php';
	$GLOBALS['wgAutoloadClasses']['SFS\ApiRequestProcessor'] = __DIR__ . '/src/ApiRequestProcessor.php';
	$GLOBALS['wgAutoloadClasses']['SFS\Output'] = __DIR__ . '/src/Output.php';

	// api modules
	$GLOBALS['wgAPIModules']['sformsselect'] = 'SFS\ApiSemanticFormsSelect';

	$GLOBALS['wgSF_SelectDir'] = dirname(__FILE__) ;
	$GLOBALS['wgSF_SelectScriptPath']  = $GLOBALS['wgScriptPath'] . '/extensions/'.basename($GLOBALS['wgSF_SelectDir']);

	$GLOBALS['wgScriptSelectCount'] = 0;
	$GLOBALS['wgSF_Select_debug'] = 0;

	$GLOBALS['wgResourceModules']['ext.sf_select.scriptselect'] = array(
		'localBasePath' => $GLOBALS['wgSF_SelectDir'],
		'remoteExtPath' => 'SemanticFormsSelect',
		'scripts' => array( 'res/scriptSelect.js' ),
		'dependencies' => array(
			'ext.semanticforms.main'
		)
	);

} );
