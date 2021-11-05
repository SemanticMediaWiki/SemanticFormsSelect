<?php

namespace SFS\Tests;

use Parser;
use ParserOutput;
use PHPUnit\Framework\TestCase;
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
class SemanticFormsSelectInputTest extends TestCase {

	private $SFSInput;


	protected function setUp(): void {
		parent::setUp();
		$value = '';
		$inputName = '';
		$isMandatory = false;
		$isDisabled = false;

		$otherArgs = [ 'template' => 'Foo', 'field' => '',
		                    'function' => 'Bar', 'is_list' => true ];

		$parserOutput = $this->getMockBuilder( ParserOutput::class )
			->disableOriginalConstructor()->getMock();

		$parser = $this->getMockBuilder( Parser::class )
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
			SemanticFormsSelectInput::class, $this->SFSInput
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
		$rsmn = [ 'ext.sf_select.scriptselect' ];

		$this->assertEquals( $rsmn, $this->SFSInput->getResourceModuleNames() );
	}

}
