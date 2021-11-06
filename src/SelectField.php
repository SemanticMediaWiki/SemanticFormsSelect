<?php

/**
 * Represents a Select Field.
 * @license GPL-2.0-or-later
 * @since 3.0.0
 * @author: Alexander Gesinn
 */

namespace SFS;

use SMWQueryProcessor as QueryProcessor;

class SelectField {

	private $mParser = null;

	/**
	 * @var (mixed|string)[]|null
	 *
	 * @psalm-var array<mixed|string>|null
	 */
	private $mValues = null;

	/**
	 * @var bool
	 */
	private $mHasStaticValues = false;

	private $mData = [];    # array with all parameters

	/**
	 * @var string
	 */
	private $mQuery = "";

	/**
	 * @var string
	 */
	private $mFunction = "";

	/**
	 * @var bool
	 */
	private $mSelectIsMultiple = false;

	/**
	 * @var false|string
	 */
	private $mSelectTemplate = "";

	/**
	 * @var false|string
	 */
	private $mSelectField = "";
	private $mValueTemplate = "";
	private $mValueField = "";

	/**
	 * @var bool
	 */
	private $mSelectRemove = false;

	/**
	 * @var bool
	 */
	private $mLabel = false;
	private $mDelimiter = ",";

	public function __construct( &$parser ) {
		$this->mParser = $parser;
	}

	/**
	 * Convenience function to process all parameters at once
	 *
	 * @return void
	 */
	public function processParameters( $input_name = "", $other_args ) {
		if ( array_key_exists( "query", $other_args ) ) {
			$this->setQuery( $other_args );
		} elseif ( array_key_exists( "function", $other_args ) ) {
			$this->setFunction( $other_args );
		}
	}

	/**
	 * getData
	 *
	 * @return array Array with all parameters
	 */
	public function getData() {
		return $this->mData;
	}

	/**
	 * @return void
	 */
	public function setQuery( $other_args ) {
		$querystr = $other_args["query"];
		$querystr = str_replace( [ "~", "(", ")" ], [ "=", "[", "]" ], $querystr );

		$this->mQuery = $querystr;
		$this->mData['selectquery'] = $querystr;

		// unparametrized query
		if ( strpos( $querystr, '@@@@' ) === false ) {
			$rawparams = explode( ";", $querystr );

			list( $query, $params ) = QueryProcessor::getQueryAndParamsFromFunctionParams(
				$rawparams, SMW_OUTPUT_WIKI, QueryProcessor::INLINE_QUERY, false
			);

			$result = QueryProcessor::getResultFromQuery(
				$query, $params, SMW_OUTPUT_WIKI, QueryProcessor::INLINE_QUERY
			);

			$this->mValues = $this->getFormattedValuesFrom( $this->mDelimiter, $result );

			$this->setHasStaticValues( true );
		}
	}

	/**
	 * @return void
	 */
	public function setFunction( $other_args ) {
		$function = $other_args["function"];
		$function = '{{#' . $function . '}}';
		$function = str_replace( [ "~", "(", ")" ], [ "=", "[", "]" ], $function );

		$this->mFunction = $function;
		$this->mData['selectfunction'] = $function;

		// unparametrized function
		if ( strpos( $function, '@@@@' ) === false ) {
			$f = str_replace( ";", "|", $function );

			$this->setValues( $this->mParser->replaceVariables( $f ) );

			$this->setHasStaticValues( true );
		}
	}

	/**
	 * @return void
	 */
	public function setSelectIsMultiple( array $other_args ) {
		$this->mSelectIsMultiple = array_key_exists( "part_of_multiple", $other_args );
		$this->mData["selectismultiple"] = $this->mSelectIsMultiple;
	}

	/**
	 * @return void
	 */
	public function setSelectTemplate( $input_name = "" ) {
		$index = strpos( $input_name, "[" );
		$this->mSelectTemplate = substr( $input_name, 0, $index );
		$this->mData['selecttemplate'] = $this->mSelectTemplate;
	}

	/**
	 * @return void
	 */
	public function setSelectField( $input_name = "" ) {
		$index = strrpos( $input_name, "[" );
		$this->mSelectField = substr( $input_name, $index + 1, strlen( $input_name ) - $index - 2 );
		$this->mData['selectfield'] = $this->mSelectField;
	}

	/**
	 * @return void
	 */
	public function setValueTemplate( array $other_args ) {
		$this->mValueTemplate = array_key_exists( "sametemplate", $other_args )
							 ? $this->mSelectTemplate
							 : $other_args["template"];
		$this->mData["valuetemplate"] = $this->mValueTemplate;
	}

	/**
	 * @return void
	 */
	public function setValueField( array $other_args ) {
		$this->mValueField = $other_args["field"];
		$this->mData["valuefield"] = $this->mValueField;
	}

	/**
	 * @return void
	 */
	public function setSelectRemove( array $other_args ) {
		$this->mSelectRemove = array_key_exists( 'rmdiv', $other_args );
		$this->mData['selectrm'] = $this->mSelectRemove;
	}

	/**
	 * @return void
	 */
	public function setLabel( array $other_args ) {
		$this->mLabel = array_key_exists( 'label', $other_args );
		$this->mData['label'] = $this->mLabel;
	}

	/**
	 * setDelimiter
	 *
	 * @param array $other_args
	 *
	 * @return void
	 */
	public function setDelimiter( array $other_args ) {
		$this->mDelimiter = $GLOBALS['wgPageFormsListSeparator'];

		if ( array_key_exists( 'sep', $other_args ) ) {
			$this->mDelimiter = $other_args['sep'];
		} else {
			// Adding Backcompatibility
			if ( array_key_exists( 'delimiter', $other_args ) ) {
				$this->mDelimiter = $other_args['delimiter'];
			}
		}

		$this->mData['sep'] = $this->mDelimiter;
	}

	public function getDelimiter() {
		return $this->mDelimiter;
	}

	/**
	 * @return (mixed|string)[]|null
	 *
	 * @psalm-return array<mixed|string>|null
	 */
	public function getValues() {
		return $this->mValues;
	}

	/**
	 * setValues
	 *
	 * @param string $values (comma separated, fully parsed list of values)
	 *
	 * @return void
	 */
	private function setValues( $values ) {
		$values = explode( $this->mDelimiter, $values );
		$values = array_map( "trim", $values );
		$values = array_unique( $values );
		$this->mValues = $values;
	}

	/**
	 * @return bool
	 */
	public function hasStaticValues() {
		return $this->mHasStaticValues;
	}

	/**
	 * @param true $StaticValues
	 *
	 * @return void
	 */
	private function setHasStaticValues( $StaticValues ) {
		$this->mHasStaticValues = $StaticValues;
	}

	/**
	 * Copied from ApiSemanticFormsSelectRequestProcessor
	 *
	 * @param string $values
	 *
	 * @return string[]
	 *
	 * @psalm-return array<string>
	 */
	private function getFormattedValuesFrom( $sep, $values ) {
		if ( strpos( $values, $sep ) === false ) {
			return [ $values ];
		}

		$values = explode( $sep, $values );
		$values = array_map( "trim", $values );
		$values = array_unique( $values );

		// TODO: sorting here will destroy any sort defined in the query, e.g. in case sorting for
		// labels (instead of mainlabel)
		// sort( $values );
		// array_unshift( $values, "" ); Unshift no needed here

		return $values;
	}
}
