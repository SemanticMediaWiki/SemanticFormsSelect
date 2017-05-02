<?php

namespace SFS\Tests;

use SFS\SemanticFormsSelect;

/**
 * @covers \SFS\SemanticFormsSelect
 * @group semantic-forms-select
 *
 * @license GNU GPL v2+
 * @since   1.3
 *
 * @author mwjames
 */
class SemanticFormsSelectTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SFS\SemanticFormsSelect',
			new SemanticFormsSelect( $parser )
		);
	}

	public function testSelect() {

		$value = '';
		$inputName = '';
		$isMandatory = false;
		$isDisabled = false;

		$otherArgs = array(
			'template' => 'Foo',
			'field' => '',
			'function' => 'Bar',
			'is_list' => true
		);

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $parserOutput ) );

		$instance = new SemanticFormsSelect( $parser );

		$this->assertInternalType(
			'string',
			$instance->select( $value, $inputName, $isMandatory, $isDisabled, $otherArgs )
		);
	}

}
