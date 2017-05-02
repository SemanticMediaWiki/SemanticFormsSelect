<?php

namespace SFS\Tests;

use SFS\ApiRequestProcessor;
use ApiMain;
use RequestContext;
use WebRequest;
use FauxRequest;

/**
 * @covers \SFS\ApiRequestProcessor
 * @group semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author mwjames
 */
class ApiRequestProcessorTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SFS\ApiRequestProcessor',
			new ApiRequestProcessor( $parser )
		);
	}

	public function testMissingParametersThrowsException() {

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApiRequestProcessor( $parser );

		$parameters = array();

		$this->setExpectedException( 'InvalidArgumentException' );
		$instance->getJsonDecodedResultValuesForRequestParameters( $parameters );
	}

	public function testJsonResultValuesFromRequestParameters() {

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ApiRequestProcessor( $parser );

		$parameters = array( 'query' => 'foo', 'sep' => ',' );

		$this->assertInternalType(
			'object',
			$instance->getJsonDecodedResultValuesForRequestParameters( $parameters )
		);
	}

}
