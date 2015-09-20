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

		$this->assertInstanceOf(
			'\SFS\Output',
			new Output()
		);
	}

	public function testAddToHeadItem() {

		$this->assertContains(
			'mw.config.set({"Foo":"\"Bar\""})',
			Output::addToHeadItem( 'Foo', 'Bar' )
		);
	}

}
