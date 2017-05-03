<?php
/**
 * Represents a Select Field.
 * @license GNU GPL v2+
 * @since 3.0.0
 * @author: Alexander Gesinn
 */

namespace SFS;

use SMWQueryProcessor as QueryProcessor;
use Parser;
use MWDebug;

class SelectField {

	private $mSelectField = array();
	private $mQuery = "";
	private $mFunction = "";

	private $mValues = null;
	private $mHasStaticValues = false;
	private $mData = array();	# array with all parameters

	/**
	 * Convenience function to process all parameters at once
	 */
	public function processParameters ($input_name = "", $other_args) {
		if ( array_key_exists( "query", $other_args ) ) {
			$this->setQuery( $other_args );
		} elseif ( array_key_exists( "function", $other_args ) ) {
			$this->setFunction( $other_args );
		}
	}

	public function setQuery( $other_args ) {
		$query = $other_args["query"];
		$query = str_replace( array( "~", "(", ")" ), array( "=", "[", "]" ), $query );

		$this->mSelectField["query"] = $query;
		$this->mQuery = $query;
		$this->mData['selectquery'] = $query;

		// unparametrized query
		if ( strpos( $query, '@@@@' ) === false ) {
			$params = explode( ";", $query );

			// there is no need to run the parser, $query has been parsed already
			//$params[0] = $wgParser->replaceVariables( $params[0] );

			$this->mValues = QueryProcessor::getResultFromFunctionParams( $params, SMW_OUTPUT_WIKI );

			$this->setHasStaticValues( true );
		}
	}

	public function setFunction( $other_args ) {
		global $wgParser;

		$function = $other_args["function"];
		$function = '{{#' . $function . '}}';
		$function = str_replace( array( "~", "(", ")" ), array( "=", "[", "]" ), $function );

		$this->mSelectField["function"] = $function;
		$this->mFunction = $function;
		$this->mData['selectfunction'] = $function;

		// unparametrized function
		if ( strpos( $function, '@@@@' ) === false ) {
			$f = str_replace( ";", "|", $function );

			$this->setValues( $wgParser->replaceVariables( $f ) );

			$this->setHasStaticValues( true );
		}
	}

	/**
	 * setSelectIsMultiple
	 * @param $other_args
	 */
	public function setSelectIsMultiple( $other_args ) {
		$this->mData["selectismultiple"] = array_key_exists( "part_of_multiple", $other_args );
	}

	/**
	 * setSelectTemplate
	 * @param string $input_name
	 */
	public function setSelectTemplate( $input_name = "" ) {
		$index = strpos( $input_name, "[" );
		$this->mData['selecttemplate'] = substr( $input_name, 0, $index );
		MWDebug::log( $this->mData['selecttemplate'] );
	}

	/**
	 * setSelectField
	 * @param string $input_name
	 */
	public function setSelectField( $input_name = "" ) {
		$index = strrpos( $input_name, "[" );
		$this->mData['selectfield'] = substr( $input_name, $index + 1, strlen( $input_name ) - $index - 2 );
		MWDebug::log( $this->mData['selectfield'] );
	}

	/**
	 * getValues
	 * @return string
	 */
	public function getValues() {
		return $this->mValues;
	}

	/**
	 * setValues
	 * @param string $values (comma separated, fully parsed list of values)
	 */
	private function setValues( $values ) {
		$values = explode( ",", $values );
		$values = array_map( "trim", $values );
		$values = array_unique( $values );
		$this->mValues = $values;
	}

	/**
	 * hasStaticValues
	 * @return boolean
	 */
	public function hasStaticValues() {
		return $this->mHasStaticValues;
	}

	/**
	 * setHasStaticValues
	 * @param boolean $StaticValues
	 */
	private function setHasStaticValues( $StaticValues ) {
		$this->mHasStaticValues = $StaticValues;
	}
}