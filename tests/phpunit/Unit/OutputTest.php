<?php

namespace SFS\Tests;

use SFS\Output;

/**
 * @covers \SFS\Output
 * @group semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author mwjames
 */
class OutputTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf( '\SFS\Output', new Output() );
	}

	public function testAddToHeadItem() {
		$data = array();
		$data['Foo'] = 'Bar';
		$data['Spam'] = 'Eggs';

		$ret = Output::addToHeadItem( $data );

		$this->assertArrayHasKey( 'Foo', $ret );
		$this->assertArrayHasKey( 'Spam', $ret );
	}
}
