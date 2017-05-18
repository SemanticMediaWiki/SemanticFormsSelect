<?php

namespace SFS\Tests;

use SFS\Output;

/**
 * @covers  \SFS\Output
 * @group   semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author  mwjames
 */
class OutputTest extends \PHPUnit_Framework_TestCase {
	private $data;

	protected function setUp() {
		parent::setUp();
		$this->data = array();
		$this->data['Foo'] = 'Bar';
		$this->data['Spam'] = 'Eggs';
	}

	protected function tearDown() {
		unset( $this->data );
		parent::tearDown();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf( '\SFS\Output', new Output() );
	}

	public function testAddToHeadItem() {
		$ret = Output::addToHeadItem( $this->data );

		$this->assertArrayHasKey( 'Foo', $ret );
		$this->assertArrayHasKey( 'Spam', $ret );
	}

	public function testCommitToParserOutput() {
		global $wgOut;
		$expected_result = '[' . json_encode( $this->data ) . ']';
		Output::commitToParserOutput();
		$this->assertEquals(
			$expected_result, $wgOut->getJsConfigVars()['sf_select']
		);
	}
}
