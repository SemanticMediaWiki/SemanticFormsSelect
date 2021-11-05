<?php

namespace SFS\Tests;

use ApiMain;
use FauxRequest;
use InvalidArgumentException;
use Parser;
use PHPUnit\Framework\TestCase;
use RequestContext;
use SFS\ApiSemanticFormsSelectRequestProcessor;
use WebRequest;

/**
 * @covers  \SFS\ApiSemanticFormsSelectRequestProcessor
 * @group   semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   3.0.0
 *
 * @author  FelixAba
 */
class ApiSemanticFormsSelectRequestProcessorTest extends TestCase {

	private $ApiSFSRP;

	protected function setUp(): void {
		parent::setUp();
		$parser = $this->getMockBuilder( Parser::class )
			->disableOriginalConstructor()->getMock();
		$this->ApiSFSRP = new ApiSemanticFormsSelectRequestProcessor( $parser );
	}

	protected function tearDown(): void {
		unset( $this->ApiSFSRP );
		parent::tearDown();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			ApiSemanticFormsSelectRequestProcessor::class, $this->ApiSFSRP
		);
	}

	public function testMissingParametersThrowsException() {

		$parameters = [];
		$this->expectException( InvalidArgumentException::class );
		$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
			$parameters
		);
	}

	public function testJsonResultValuesFromRequestParameters() {

		$parameters = [ 'query' => 'foo', 'sep' => ',' ];

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testJsonResultValuesFromRequestParameters_doProcessQueryFor(
	) {

		$parameters = [ 'approach' => 'smw', 'query' => 'foo, baa, gaah',
		                     'sep'      => ',' ];

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testSetDebugFlag() {
		$this->ApiSFSRP->setDebugFlag( true );
		$parameters = [ 'query' => 'foo , function', 'sep' => ',' ];

		$this->assertInternalType(
			'object',
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testSetDebugFlag_doProcessQueryFor() {
		$this->ApiSFSRP->setDebugFlag( true );
		$parameters = [ 'approach' => 'smw', 'query' => 'my Query,query2',
		                     'sep'      => ',' ];

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
		$result = [ "", "my Query", "query2" ];
		$formattedValues = $this->invokeMethod(
			$this->ApiSFSRP, 'getFormattedValuesFrom', [ $sep, $values ]
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
		array $parameters = []
	) {
		$reflection = new \ReflectionClass( get_class( $object ) );
		$method = $reflection->getMethod( $methodName );
		$method->setAccessible( true );

		return $method->invokeArgs( $object, $parameters );
	}


}
