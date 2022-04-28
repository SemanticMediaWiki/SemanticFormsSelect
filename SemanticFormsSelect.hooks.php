<?php

namespace SFS;

/**
 * @license GNU GPL v2+
 * @since 3.0
 * @author: Alexander Gesinn
 */
class Hooks {
	/**
	 * Register Page Forms Input Type
	 *
	 * This is attached to the MediaWiki 'ParserFirstCallInit' hook.
	 *
	 * @param $parser Parser
	 * @return bool
	 */
	public static function onSemanticFormsSelectSetup ( & $parser ) {
		if ( !defined( 'PF_VERSION' ) ) {
			die( '<b>Error:</b><a href="https://github.com/SemanticMediaWiki/SemanticFormsSelect/">Semantic Forms Select</a> requires the <a href="https://www.mediawiki.org/wiki/Extension:PageForms">Page Forms</a> extension. Please install and activate this extension first.' );
		}

		if ( isset( $GLOBALS['wgPageFormsFormPrinter'] ) ) {
			$GLOBALS['wgPageFormsFormPrinter']->registerInputType( \SFS\SemanticFormsSelectInput::class );
		}

		return true;
	}

	public static function onRegistration() {
		if ( isset( $GLOBALS['wgAPIModules'] ) ) {
			$GLOBALS['wgAPIModules']['sformsselect'] = \SFS\ApiSemanticFormsSelect::class;
		}
	}

	/**
	 * Hook: ResourceLoaderTestModules
	 * @param array &$modules
	 */
	public static function onResourceLoaderTestModules( array &$modules ) {
		$modules['qunit']['ext.sfs.unit'] = [
			'scripts' => [
				'res/sfs.js',
				'tests/qunit/unit/sfs.test.js'
			],
			'dependencies' => [
				'ext.pageforms.originalValueLookup'
			],
			'localBasePath' => __DIR__,
			'remoteExtPath' => 'SemanticFormsSelect',
		];
	}
}
