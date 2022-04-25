<?php

/**
 * @license GNU GPL v2+
 * @since 1.3
 *
 * @author Jason Zhang
 * @author Toni Hermoso Pulido
 * @author mwjames
 */

namespace SFS;

use Parser;
use SMWQueryProcessor;
use InvalidArgumentException;
use MWDebug;

class ApiSemanticFormsSelectRequestProcessor {

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var boolean
	 */
	private $debugFlag = false;

	private $getSmwResultFromFunctionParams;

	/**
	 * @param Parser $parser
	 * @param null $getSmwResultFromFunctionParams
	 * @since 1.3
	 *
	 */
	public function __construct( Parser $parser, $getSmwResultFromFunctionParams = null ) {
		$this->parser = $parser;
		$this->getSmwResultFromFunctionParams = $getSmwResultFromFunctionParams
			?? '\SFS\ApiSemanticFormsSelectRequestProcessor::defaultGetSmwResultFromFunctionParams';
	}

	/**
	 * @since 1.3
	 *
	 * @param boolean $debugFlag
	 */
	public function setDebugFlag( $debugFlag ) {
		$this->debugFlag = $debugFlag;
	}

	/**
	 * @since 1.3
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	public function getJsonDecodedResultValuesForRequestParameters( array $parameters ) {

		if ( !isset( $parameters['query'] ) || !isset( $parameters['sep'] ) ) {
			throw new InvalidArgumentException( 'Missing an query parameter' );
		}

		$this->parser->firstCallInit();
		$json = [];

		if ( isset( $parameters['approach'] ) && $parameters['approach'] == 'smw' ) {
			$json = $this->doProcessQueryFor( $parameters['query'], $parameters['sep'] );
		} else {
			$json = $this->doProcessFunctionFor( $parameters['query'], $parameters['sep'] );
		}

		// I have no idea why we first encode and and then decode here??

		return json_decode( $json );
	}

	private function doProcessQueryFor( $querystr, $sep = "," ) {

		$querystr = str_replace(
			[ "&lt;", "&gt;", "sep=;" ],
			[ "<", ">", "sep={$sep};" ],
			$querystr
		);

		$rawparams = $this->extractRawParameters( $querystr );
		$f = str_replace( ";", "|", $rawparams[0] );
		$rawparams[0] = $this->parser->replaceVariables( $f );

		if ( $this->debugFlag ) {
			error_log( implode( "|", $rawparams ) );
		}

		$result = ($this->getSmwResultFromFunctionParams)($rawparams);

		$values = $this->getFormattedValuesFrom( $sep, $result );

		return json_encode( [
			"values" => $values,
			"count"  => count( $values )
		] );
	}

	private function extractRawParameters( $querystr ) {
		$ensureParameter = function($name, $value) use (&$rawparams) {
			$rawparams = array_filter($rawparams, function($param) use ($name) {
				return substr_compare( $param, "$name=", 0, strlen( "$name=" ) ) !== 0;
			});
			if ($value !== null)
				$rawparams[] = "$name=$value";
		};

		$rawparams = explode( ";", $querystr );
		// The JavaScript part expects plainlist format for parsing
		$ensureParameter('format', 'plainlist');

		return $rawparams;
	}

	private function doProcessFunctionFor( $query, $sep = "," ) {

		$query = str_replace(
			[ "&lt;", "&gt;", "sep=;" ],
			[ "<", ">", "sep={$sep};" ],
			$query
		);

		$f = str_replace( ";", "|", $query );

		if ( $this->debugFlag ) {
			error_log( $f );
		}

		$values = $this->getFormattedValuesFrom(
			$sep,
			$this->parser->replaceVariables( $f )
		);

		return json_encode( [
			"values" => $values,
			"count"  => count( $values )
		] );
	}

	private function getFormattedValuesFrom( $sep, $values ) {

		if ( strpos( $values, $sep ) === false ) {
			return [ $values ];
		}

		$values = explode( $sep, $values );
		$values = array_map( "trim", $values );
		$values = array_unique( $values );

		// TODO: sorting here will destroy any sort defined in the query, e.g. in case sorting for labels (instead of mainlable)
		//sort( $values );
		array_unshift( $values, "" );

		return $values;
	}

	/**
	 * @param $rawparams
	 * @return string
	 */
	private static function defaultGetSmwResultFromFunctionParams( $rawparams ): string {
		list( $query, $params ) =
			SMWQueryProcessor::getQueryAndParamsFromFunctionParams( $rawparams, SMW_OUTPUT_WIKI,
				SMWQueryProcessor::INLINE_QUERY, false );

		$result =
			SMWQueryProcessor::getResultFromQuery( $query, $params, SMW_OUTPUT_WIKI,
				SMWQueryProcessor::INLINE_QUERY );

		return $result;
	}
}
