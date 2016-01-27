<?php

/**
 * @see https://github.com/SemanticMediaWiki/SemanticFormsSelect/
 * @link https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect
 *
 * @defgroup SFS SemanticFormsSelect
 */

if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.23c', 'lt' ) ) {
	die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">SemanticFormsSelect</a> is only compatible with MediaWiki 1.23 or above. You need to upgrade MediaWiki first.' );
}

if ( isset( $wgWikimediaTravisCI ) && $wgWikimediaTravisCI == true ) {
	if ( is_readable( __DIR__ . '/../../vendor/autoload.php' ) ) {
		require_once __DIR__ . '/../../vendor/autoload.php';
	}
} elseif ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

$GLOBALS['wgExtensionFunctions'][] = function() {
	if ( version_compare( $GLOBALS['wgVersion'], '1.27c', '>' ) ) {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'SemanticForms' ) ) {
			die( '<b>Error:</b> <a href="https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect">Semantic Forms Selects</a> is a Semantic Forms extension. You need to install <a href="https://www.mediawiki.org/wiki/Extension:Semantic_Forms">Semantic Forms</a> first.' );
		}
	}
};

// Do not initialize more than once.
if ( defined( 'SFS_VERSION' ) ) {
	return 1;
}

define( 'SFS_VERSION', '1.3.0' );

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'SemanticFormsSelect' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$GLOBALS['wgMessagesDirs']['SemanticFormsSelect'] = __DIR__ . '/i18n';
	/* wfWarn(
		'Deprecated PHP entry point used for SemanticFormsSelect extension. Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return;
}

/**
 * @codeCoverageIgnore
 */
call_user_func( function() {

	$GLOBALS['wgExtensionCredits']['semantic'][] = array(
		'path' => __FILE__,
		'name' => 'Semantic Forms Select',
		'author' =>array( 'Jason Zhang', 'Toni Hermoso Pulido', '...' ),
		'url' => 'https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect',
		'descriptionmsg' => 'semanticformsselect-desc',
		'version'  => SFS_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	$GLOBALS['wgMessagesDirs']['SemanticFormsSelect'] = __DIR__ . '/i18n';

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
