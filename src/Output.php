<?php

/**
 * @license GPL-2.0-or-later
 * @since 1.3
 *
 * @author mwjames
 * @author Alexander Gesinn
 */

namespace SFS;

class Output {

	/**
	 * @var array
	 */
	private static $headItems = [];

	/**
	 * Add an array of SF_Select field parameters as defined in Page Form's field tag.
	 *
	 * This will later be added to $wgOut so that JS can access it via mw.config.get
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public static function addToHeadItem( array $data = [] ) {
		return self::$headItems[] = $data;
	}

	/**
	 * Commit all SF_Select field parameters to Output
	 *
	 * @return void
	 */
	public static function commitToParserOutput() {
		global $wgOut;	# is there a better way to get $output/$parser without using a global? (testability!)

		// to be used in JS like:
		// var SFSelect_fobjs = $.parseJSON( mw.config.get( 'sf_select' ) );
		$wgOut->addJsConfigVars( 'sf_select', json_encode( self::$headItems ) );
	}
}
