<?php

namespace SFS;

use ResourceLoader;
use ParserOutput;

/**
 * @license GNU GPL v2+
 * @since 1.3
 *
 * @author mwjames
 */
class Output {

	/**
	 * @var array
	 */
	private static $headItems = array();

	/**
	 * @var array
	 */
	private static $resourceModules = array();

	/**
	 * @since 1.3
	 *
	 * @param string $moduleName
	 */
	public static function addModule( $moduleName ) {
		self::$resourceModules[$moduleName] = $moduleName;
	}

	/**
	 * @since 1.3
	 *
	 * @param string $id
	 * @param string $data
	 */
	public static function addToHeadItem( $id, $data = '' ) {
		return self::$headItems[$id] = self::makeVariablesScript( array( $id => json_encode( $data ) ) );
	}

	/**
	 * @since 1.3
	 *
	 * @param ParserOutput $parserOutput
	 */
	public static function commitToParserOutput( ParserOutput $parserOutput ) {

		foreach ( self::$headItems as $key => $item ) {
			$parserOutput->addHeadItem( "\t\t" . $item . "\n", $key );
		}

		$parserOutput->addModules( array_values( self::$resourceModules ) );

		self::$resourceModules = array();
		self::$headItems = array();
	}

	private static function makeVariablesScript( $data ) {

		if ( $data ) {
			return \Html::inlineScript(
				ResourceLoader::makeLoaderConditionalScript(
					ResourceLoader::makeConfigSetScript( $data )
				)
			);
		}

		return '';
	}

}
