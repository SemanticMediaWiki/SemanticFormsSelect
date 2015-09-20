<?php

namespace SFS\Tests;

use SFS\ApiSemanticFormsSelect;
use ApiMain;
use RequestContext;
use WebRequest;
use FauxRequest;

/**
 * @covers \SFS\ApiSemanticFormsSelect
 * @group semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author mwjames
 */
class ApiSemanticFormsSelectTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$apiMain = new ApiMain( $this->newRequestContext( array() ), true );

		$instance = new ApiSemanticFormsSelect(
			$apiMain,
			'sformsselect'
		);

		$this->assertInstanceOf(
			'\SFS\ApiSemanticFormsSelect',
			$instance
		);
	}

	public function testExecute() {

		$parameters = array(
			'action'   => 'sformsselect',
			'approach' => 'smw',
			'query'    => 'abc',
			'sep'      => ','
		);

		$apiMain = new ApiMain( $this->newRequestContext( $parameters ), true );

		$instance = new ApiSemanticFormsSelect(
			$apiMain,
			'sformsselect'
		);

		$this->assertTrue(
			$instance->execute()
		);
	}

	private function newRequestContext( $request = array() ) {

		$context = new RequestContext();

		if ( $request instanceof WebRequest ) {
			$context->setRequest( $request );
		} else {
			$context->setRequest( new FauxRequest( $request, true ) );
		}

		return $context;
	}

}
