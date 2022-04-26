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
class SemanticFormsSelectInputTest extends \PHPUnit_Framework_TestCase {

	private $SFSInput;


	protected function setUp(): void {
		parent::setUp();
		$value = '';
		$inputName = '';
		$isMandatory = false;
		$isDisabled = false;

		$otherArgs = [ 'template' => 'Foo', 'field' => '',
		                    'function' => 'Bar', 'is_list' => true ];

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()->getMock();

		$parser->expects( $this->any() )->method( 'getOutput' )->will(
				$this->returnValue( $parserOutput )
			);
		$this->SFSInput = new SemanticFormsSelectInput(
			$value, $inputName, $isMandatory, $isDisabled, $otherArgs
		);
	}

	protected function tearDown(): void {
		unset( $this->SelectField );
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

		$this->assertEquals( [ 'ext.sfs', 'ext.pageforms.originalValueLookup' ],
			$result );
	}
}
