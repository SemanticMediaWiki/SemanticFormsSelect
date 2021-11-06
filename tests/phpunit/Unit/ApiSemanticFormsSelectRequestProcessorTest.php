<?php

namespace SFS\Tests;

use ApiMain;
use FauxRequest;
use InvalidArgumentException;
use Parser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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

		$this->assertIsObject(
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testJsonResultValuesFromRequestParameters_doProcessQueryFor(
	) {

		$parameters = [ 'approach' => 'smw', 'query' => 'foo, baa, gaah',
		                     'sep'      => ',' ];

		$this->assertIsObject(
			$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters(
				$parameters
			)
		);
	}

	public function testSetDebugFlag() {
		$this->ApiSFSRP->setDebugFlag( true );
		$parameters = [ 'query' => 'foo , function', 'sep' => ',' ];

		$capture = tmpfile();
		$errHandler = ini_set( 'error_log', stream_get_meta_data( $capture )['uri'] );

		$this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters( $parameters );

		ini_set( 'error_log', $errHandler );
		$output = stream_get_contents( $capture );

		$this->assertStringContainsString(
			$parameters['query'], $output, "proper output"
		);
	}

	public function testSetDebugFlag_doProcessQueryFor() {
		$this->ApiSFSRP->setDebugFlag( true );
		$parameters = [ 'approach' => 'smw', 'query' => 'my Query,query2',
		                     'sep'      => ',' ];

		$capture = tmpfile();
		$errHandler = ini_set( 'error_log', stream_get_meta_data( $capture )['uri'] );

		$obj = $this->ApiSFSRP->getJsonDecodedResultValuesForRequestParameters( $parameters );

		ini_set( 'error_log', $errHandler );
		$output = stream_get_contents( $capture );

		$this->assertIsObject( $obj ); // stdClass, so assertInstanceOf isn't possible
		$this->assertStringMatchesFormat(
			"[%d-%c%c%c-%d %d:%d:%d %s]%c%c", $output, "No debug output"
		);
		$this->assertStringNotMatchesFormat(
			"[%d-%c%c%c-%d %d:%d:%d %s]%c%c%c", $output, "No extra debug output"
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
		$reflection = new ReflectionClass( get_class( $object ) );
		$method = $reflection->getMethod( $methodName );
		$method->setAccessible( true );

		return $method->invokeArgs( $object, $parameters );
	}


}
