<?php

namespace SFS\Tests;

use SFS\ApiSemanticFormsSelect;
use ApiMain;
use RequestContext;
use WebRequest;
use FauxRequest;

/**
 * @covers  \SFS\ApiSemanticFormsSelect
 * @group   semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author  mwjames
 */
class ApiSemanticFormsSelectTest extends \PHPUnit_Framework_TestCase {

	private $ApiSFS;
	private $ApiMain;

	protected function setUp() {
		parent::setUp();
		$parameters = array( 'action' => 'sformsselect', 'approach' => 'smw',
		                     'query'  => 'abc', 'sep' => ',' );

		$this->ApiMain = new ApiMain(
			$this->newRequestContext( $parameters ), true
		);
		$this->ApiSFS = new ApiSemanticFormsSelect(
			$this->ApiMain, 'sformsselect'
		);
	}

	protected function tearDown() {
		unset( $this->ApiSFS );
		unset( $this->ApiMain );
		parent::tearDown();
	}


	public function testCanConstruct() {

		$apiMain = new ApiMain( $this->newRequestContext( array() ), true );

		$instance = new ApiSemanticFormsSelect(
			$apiMain, 'sformsselect'
		);

		$this->assertInstanceOf(
			'\SFS\ApiSemanticFormsSelect', $this->ApiSFS
		);
	}

	public function testExecute() {

		$this->assertTrue(
			$this->ApiSFS->execute()
		);
	}

	public function testGetDescription() {
		$tdata = array( 'API for providing SemanticFormsSelect values' );
		$this->assertEquals( $this->ApiSFS->getDescription(), $tdata );
	}

	public function testGetParamDescription() {
		$tdata = array( 'approach' => 'The actual approach: function or smw',
		                'query'    => 'The query of the former' );
		$this->assertEquals( $this->ApiSFS->getParamDescription(), $tdata );
	}

	public function testGetVersion() {
		$tdata = 'SFS\ApiSemanticFormsSelect: 1.1';
		$this->assertEquals( $this->ApiSFS->getVersion(), $tdata );
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
