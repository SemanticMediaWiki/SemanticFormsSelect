<?php

namespace SFS\Tests;

use SFS\ApiSemanticFormsSelectRequestProcessor;
use ApiMain;
use RequestContext;
use WebRequest;
use FauxRequest;

/**
 * @covers  \SFS\ApiSemanticFormsSelectRequestProcessor
 * @group   semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author  mwjames
 */
class ApiSemanticFormsSelectRequestProcessorTest
	extends \PHPUnit_Framework_TestCase {

	private $ApiSFSRP;

	protected function setUp() {
		parent::setUp();
		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()->getMock();
		$this->ApiSFSRP = new ApiSemanticFormsSelectRequestProcessor( $parser );
	}

	protected function tearDown() {
		unset( $this->ApiSFSRP );
		parent::tearDown();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SFS\ApiSemanticFormsSelectRequestProcessor', $this->ApiSFSRP
		);
	}

	public function testMissingParametersThrowsException() {

		$parameters = array();

		$this->setExpectedException( 'InvalidArgumentException' );
		$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
			$parameters
		);
	}

	public function testJsonResultValuesFromRequestParameters() {

		$parameters = array( 'query' => 'foo', 'sep' => ',' );

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testJsonResultValuesFromRequestParameters_doProcessQueryFor(
	) {

		$parameters = array( 'approach' => 'smw', 'query' => 'foo, baa, gaah',
		                     'sep'      => ',' );

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testSetDebugFlag() {
		$this->ApiSFSRP->setDebugFlag( true );
		$parameters = array( 'query' => 'foo , function', 'sep' => ',' );

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testSetDebugFlag_doProcessQueryFor() {
		$this->ApiSFSRP->setDebugFlag( true );
		$parameters = array( 'approach' => 'smw', 'query' => 'my Query,query2',
		                     'sep'      => ',' );

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testGetFormattedValuesFrom() {
		$sep = ",";
		$values = "my Query,query2";
		$result = array( "", "my Query", "query2" );
		$formattedValues = $this->invokeMethod(
			$this->ApiSFSRP, 'getFormattedValuesFrom', array( $sep, $values )
		);
		$this->assertEquals( $result, $formattedValues );
	}

	/**
	 * Call protected/private method of a class.
	 *
	 * @param object &$object Instantiated object that we will run method on.
	 * @param string $methodName Method name to call
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	public function invokeMethod( &$object, $methodName,
		array $parameters = array()
	) {
		$reflection = new \ReflectionClass( get_class( $object ) );
		$method = $reflection->getMethod( $methodName );
		$method->setAccessible( true );

		return $method->invokeArgs( $object, $parameters );
	}


}
