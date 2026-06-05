<?php

namespace SFS;

use Parser;

/**
 * @license GPL-2.0-or-later
 * @since 3.0
 * @author Alexander Gesinn
 */
class Hooks {

	/**
	 * Register the Page Forms input type.
	 *
	 * Attached to the MediaWiki 'ParserFirstCallInit' hook.
	 *
	 * @param Parser $parser
	 * @return bool
	 */
	public static function onSemanticFormsSelectSetup( Parser $parser ) {
		if ( isset( $GLOBALS['wgPageFormsFormPrinter'] ) ) {
			$GLOBALS['wgPageFormsFormPrinter']->registerInputType( SemanticFormsSelectInput::class );
		}

		return true;
	}
}
