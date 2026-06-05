<?php

/**
 * API modules to communicate with the back-end
 *
 * @license GPL-2.0-or-later
 * @since 1.2
 *
 * @author Jason Zhang
 * @author Toni Hermoso Pulido
 */

namespace SFS;

use ApiBase;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\Title;
use Parser;
use ParserOptions;

class ApiSemanticFormsSelect extends ApiBase {

	/**
	 * @see ApiBase::execute
	 */
	public function execute() {
		$parser = $this->getParser();

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

	/**
	 * @return Parser
	 */
	protected function getParser(): Parser {
		$parser = MediaWikiServices::getInstance()->getParserFactory()->create();

		// The request processor uses Parser::replaceVariables() directly rather
		// than a full parse, so the parser must be put into a usable state first.
		// startExternalParse() is the supported public entry point for this: it
		// sets the page and options, sets the output type, and clears parser
		// state. Without it, properties such as Parser::$ot and
		// Parser::$mIncludeSizes are uninitialized and fatal under MW 1.43+
		// (https://github.com/SemanticMediaWiki/SemanticFormsSelect/issues/139).
		$parser->startExternalParse(
			Title::newFromText( 'NO TITLE' ),
			new ParserOptions( $this->getUser() ),
			Parser::OT_HTML
		);

		return $parser;
	}
}
