<?php

/**
 * API modules to communicate with the back-end
 *
 * @license GNU GPL v2+
 * @since 1.2
 *
 * @author Jason Zhang
 * @author Toni Hermoso Pulido
 */

namespace SFS;

use ApiBase;
use Parser;
use ParserOptions;
use ParserOutput;
use Title;

class ApiSemanticFormsSelect extends ApiBase {

	/**
	 * @see ApiBase::execute
	 */
	public function execute() {
		$parser = $this->getParser();
		$parser->setTitle( Title::newFromText( 'NO TITLE' ) );
		$parser->mOptions = new ParserOptions();
		$parser->mOutput = new ParserOutput();

		$apiRequestProcessor = new \SFS\ApiSemanticFormsSelectRequestProcessor( $parser );
		$apiRequestProcessor->setDebugFlag( $GLOBALS['wgSF_Select_debug'] );

		$resultValues = $apiRequestProcessor->getJsonDecodedResultValuesForRequestParameters(
			$this->extractRequestParams()
		);

		$result = $this->getResult();
		$result->setIndexedTagName( $resultValues->values, 'value' );
		$result->addValue( $this->getModuleName(), 'values', $resultValues->values );
		$result->addValue( $this->getModuleName(), 'count', $resultValues->count );

		return true;
	}

	/**
	 * @see ApiBase::getAllowedParams
	 */
	public function getAllowedParams() {
		return [
			'approach' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			],
			'query' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			],
			'sep' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => false
			]
		];
	}

	/**
	 * @see ApiBase::getDescription
	 */
	public function getDescription() {
		return [
			'API for providing SemanticFormsSelect values'
		];
	}

	/**
	 * @see ApiBase::getParamDescription
	 */
	public function getParamDescription() {
		return [
			'approach' => 'The actual approach: function or smw',
			'query' => 'The query of the former'
		];
	}

	/**
	 * @see ApiBase::getVersion
	 */
	public function getVersion() {
		return __CLASS__ . ': 1.1';
	}

	// Compatibility helper for MW < 1.32
	private function getParser(): Parser {
		if ( class_exists( \MediaWiki\MediaWikiServices::class ) ) {
			$services = \MediaWiki\MediaWikiServices::getInstance();
			if ( is_callable( $services, 'getParserFactory' ) ) {
				return $services->getParserFactory()->create();
			}
		}
		return new Parser( $GLOBALS['wgParserConf'] );
	}

}
