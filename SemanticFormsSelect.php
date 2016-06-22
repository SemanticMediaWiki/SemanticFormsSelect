<?php

use SFS\HookRegistry;

/**
 * @see https://github.com/SemanticMediaWiki/SemanticFormsSelect/
 *
 * @defgroup SemanticFormsSelect Semantic Forms Select
 * @codeCoverageIgnore
 */
class SemanticFormsSelect {

	/**
	 * @since 1.0
	 */
	public static function initExtension() {

		define( 'SFS_VERSION', true );

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
				'ext.semanticforms.main'
			)
		);
	}

	/**
	 * @since 1.0
	 */
	public static function onExtensionFunction() {

		if ( !defined( 'SF_VERSION' ) ) {
			die( '<b>Error:</b><a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">Semantic Forms Select</a> requires the <a href="https://www.mediawiki.org/wiki/Extension:SemanticForms">Semantic Forms</a> extension. Please install and activate this extension first.' );
		}

		if ( isset( $GLOBALS['sfgFormPrinter'] )) {
			$GLOBALS['sfgFormPrinter']->setInputTypeHook( 'SF_Select', '\SFS\SemanticFormsSelect::init', array() );
		}
	}

	/**
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		$extensionData = ExtensionRegistry::getInstance()->getAllThings();

		if ( isset( $extensionData['Semantic Forms Select'] ) ) {
			return $extensionData['Semantic Forms Select']['version'];
		}

		return null;
	}

}
