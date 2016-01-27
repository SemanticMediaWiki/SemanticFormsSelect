<?php

namespace SFS;

use ExtensionRegistry;

class SemanticFormsSelectHooks {

	public static function onCallback() {
		// Do not initialize more than once.
		if ( defined( 'SFS_VERSION' ) ) {
			return 1;
		}

		define( 'SFS_VERSION', '1.3.0' );
	}

	public static function onExtensionFunction() {
		if ( !\ExtensionRegistry::getInstance()->isLoaded( 'SemanticForms' ) ) {
			die( '<b>Error:</b> <a href="https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect">Semantic Forms Selects</a> is a Semantic Forms extension. You need to install <a href="https://www.mediawiki.org/wiki/Extension:Semantic_Forms">Semantic Forms</a> first.' );
		}

		$GLOBALS['sfgFormPrinter']->setInputTypeHook( 'SF_Select', '\SFS\SemanticFormsSelect::init', array() );
	}

}