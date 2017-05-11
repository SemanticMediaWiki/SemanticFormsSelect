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

		if ( isset( $GLOBALS['wgPageFormsFormPrinter'] )) {
			$GLOBALS['wgPageFormsFormPrinter']->registerInputType( '\SFS\SemanticFormsSelectInput' );
		}

		return true;
	}
}