<?php

namespace SFS\Tests;

use PHPUnit\Framework\TestCase;
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
class OutputTest extends TestCase {
	private $data;

	protected function setUp(): void {
		parent::setUp();
		$this->data = [];
		$this->data['Foo'] = 'Bar';
		$this->data['Spam'] = 'Eggs';
	}

	protected function tearDown(): void {
		unset( $this->data );
		parent::tearDown();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf( Output::class, new Output() );
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
