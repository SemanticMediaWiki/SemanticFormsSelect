<?php

namespace SFS\Tests;

use SFS\SemanticFormsSelectInput;

/**
 * @covers  \SFS\SemanticFormsSelectInput
 * @group   semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   3.0.0
 *
 * @author  FelixAba
 */
class SemanticFormsSelectInputTest extends \PHPUnit\Framework\TestCase {

	private $SFSInput;


	protected function setUp(): void {
		parent::setUp();

		$otherArgs = [ 'template' => 'Foo', 'field' => '',
		                    'function' => 'Bar', 'is_list' => true ];

		// $inputNumber, $curValue, $inputName, $disabled, $otherArgs
		$this->SFSInput = new SemanticFormsSelectInput(
			1, '', 'TestTemplate[Field]', false, $otherArgs
		);
	}

	protected function tearDown(): void {
		unset( $this->SFSInput );
		parent::tearDown();
	}

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SFS\SemanticFormsSelectInput', $this->SFSInput
		);
	}

	public function testGetHTMLText() {
		$this->assertIsString(
			$this->SFSInput->getHtmlText()
		);
	}

	public function testGetName() {
		$this->assertEquals(
			'SF_Select', $this->SFSInput->getName()
		);
	}

	public function testGetParameters() {
		$this->assertIsArray( $this->SFSInput->getParameters() );
	}


	public function testGetResourceModuleNames() {
		$result = $this->SFSInput->getResourceModuleNames();

		$this->assertEquals( [ 'ext.sfs' ], $result );
	}
}
