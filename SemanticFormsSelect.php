<?php

use SFS\HookRegistry;

/**
 * @see https://github.com/SemanticMediaWiki/SemanticFormsSelect/
 *
 * @defgroup SemanticFormsSelect Semantic Forms Select
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( defined( 'SFS_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

SemanticFormsSelect::initExtension();

$GLOBALS['wgExtensionFunctions'][] = function() {
	SemanticFormsSelect::onExtensionFunction();
};

/**
 * @codeCoverageIgnore
 */
class SemanticFormsSelect {

	/**
	 * @since 1.0
	 */
	public static function initExtension() {

		define( 'SFS_VERSION', '2.0.0-alpha' );

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
		$GLOBALS['wgResourceModules']['ext.sf_select.scriptselect'] = array(
			'localBasePath' => __DIR__ ,
			'remoteExtPath' => 'SemanticFormsSelect',
			'position' => 'bottom',
			'scripts' => array( 'res/scriptSelect.js' ),
			'dependencies' => array(
				'ext.pageforms.main'
			)
		);
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {

		if ( !defined( 'SF_VERSION' ) ) {
			die( '<b>Error:</b><a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">Semantic Forms Select</a> requires the <a href="https://www.mediawiki.org/wiki/Extension:PageForms">Semantic Forms</a> extension. Please install and activate this extension first.' );
		}

		if ( isset( $GLOBALS['wgPageFormsFormPrinter'] )) {
			$GLOBALS['wgPageFormsFormPrinter']->setInputTypeHook( 'SF_Select', '\SFS\SemanticFormsSelect::init', array() );
		}
	}

	/**
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		return SFS_VERSION;
	}

}
