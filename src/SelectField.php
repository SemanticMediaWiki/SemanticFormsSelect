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

	private $mParser = null;

	//private $mSelectField = array();
	private $mValues = null;
	private $mHasStaticValues = false;

	private $mData = [];    # array with all parameters
	private $mQuery = "";
	private $mFunction = "";
	private $mSelectIsMultiple = false;
	private $mSelectTemplate = "";
	private $mSelectField = "";
	private $mValueTemplate = "";
	private $mValueField = "";
	private $mSelectRemove = false;
	private $mLabel = false;
	private $mDelimiter = ",";

	public function __construct( & $parser ) {
		$this->mParser = $parser;
	}

	/**
	 * Convenience function to process all parameters at once
	 */
	public function processParameters( $other_args, $input_name = "" ) {
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

	public function setQuery( $other_args ) {
		$querystr = $other_args["query"];
		$querystr = str_replace( [ "~", "(", ")" ], [ "=", "[", "]" ], $querystr );

		//$this->mSelectField["query"] = $query;
		$this->mQuery = $querystr;
		$this->mData['selectquery'] = $querystr;

		// unparametrized query
		if ( strpos( $querystr, '@@@@' ) === false ) {
			$rawparams = explode( ";", $querystr );

			// there is no need to run the parser, $query has been parsed already
			//$params[0] = $wgParser->replaceVariables( $params[0] );

			list( $query, $params ) = QueryProcessor::getQueryAndParamsFromFunctionParams( $rawparams, SMW_OUTPUT_WIKI, QueryProcessor::INLINE_QUERY, false );
			
			$result = QueryProcessor::getResultFromQuery( $query, $params, SMW_OUTPUT_WIKI, QueryProcessor::INLINE_QUERY );
		
			$this->mValues = $this->getFormattedValuesFrom( $this->mDelimiter, $result );
		
			$this->setHasStaticValues( true );
		}
	}

	public function setFunction( $other_args ) {
		#global $wgParser;

		$function = $other_args["function"];
		$function = '{{#' . $function . '}}';
		$function = str_replace( [ "~", "(", ")" ], [ "=", "[", "]" ], $function );

		//$this->mSelectField["function"] = $function;
		$this->mFunction = $function;
		$this->mData['selectfunction'] = $function;

		// unparametrized function
		if ( strpos( $function, '@@@@' ) === false ) {
			$f = str_replace( ";", "|", $function );

			$this->setValues( $this->mParser->replaceVariables( $f ) );

			$this->setHasStaticValues( true );
		}
	}

	public function setSelectIsMultiple( Array $other_args ) {
		$this->mSelectIsMultiple = array_key_exists( "part_of_multiple", $other_args );
		$this->mData["selectismultiple"] = $this->mSelectIsMultiple;
	}

	public function setSelectTemplate( $input_name = "" ) {
		$index = strpos( $input_name, "[" );
		$this->mSelectTemplate = substr( $input_name, 0, $index );
		$this->mData['selecttemplate'] = $this->mSelectTemplate;
	}

	public function setSelectField( $input_name = "" ) {
		$index = strrpos( $input_name, "[" );
		$this->mSelectField = substr( $input_name, $index + 1, strlen( $input_name ) - $index - 2 );
		$this->mData['selectfield'] = $this->mSelectField;
	}

	public function setValueTemplate( Array $other_args ) {
		$this->mValueTemplate =
			array_key_exists( "sametemplate", $other_args ) ? $this->mSelectTemplate : $other_args["template"];
		$this->mData["valuetemplate"] = $this->mValueTemplate;
	}

	public function setValueField( Array $other_args ) {
		$this->mValueField = $other_args["field"];
		$this->mData["valuefield"] = $this->mValueField;

	}

	public function setSelectRemove( Array $other_args ) {
		$this->mSelectRemove = array_key_exists( 'rmdiv', $other_args );
		$this->mData['selectrm'] = $this->mSelectRemove;
	}

	public function setLabel( Array $other_args ) {
		$this->mLabel = array_key_exists( 'label', $other_args );
		$this->mData['label'] = $this->mLabel;
	}

	/**
	 * setDelimiter
	 * @param array $other_args
	 */
	public function setDelimiter( Array $other_args ) {
		
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

	public function getValues() {
		return $this->mValues;
	}

	/**
	 * setValues
	 * @param string $values (comma separated, fully parsed list of values)
	 */
	private function setValues( $values ) {
		$values = explode( $this->mDelimiter, $values );
		$values = array_map( "trim", $values );
		$values = array_unique( $values );
		$this->mValues = $values;
	}

	public function hasStaticValues() {
		return $this->mHasStaticValues;
	}

	private function setHasStaticValues( $StaticValues ) {
		$this->mHasStaticValues = $StaticValues;
	}
	
	/** Copied from ApiSemanticFormsSelectRequestProcessor */
	
	private function getFormattedValuesFrom( $sep, $values ) {

		if ( strpos( $values, $sep ) === false ) {
			return [ $values ];
		}

		$values = explode( $sep, $values );
		$values = array_map( "trim", $values );
		$values = array_unique( $values );

		// TODO: sorting here will destroy any sort defined in the query, e.g. in case sorting for labels (instead of mainlable)
		//sort( $values );
		// array_unshift( $values, "" ); Unshift no needed here

		return $values;
	}
}
