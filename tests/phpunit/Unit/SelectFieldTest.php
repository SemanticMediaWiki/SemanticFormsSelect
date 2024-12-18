<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SFS\Tests;

use MediaWiki\MediaWikiServices;
use Parser;
use SFS\SelectField;

use ParserOptions;
use Title;

/**
 * @covers \SFS\SelectField
 * @group  semantic-forms-select
 * @author FelixAba
 */
class SelectFieldTest extends \PHPUnit\Framework\TestCase {
	private $selectField;
	
	private $other_args_query_parametrized = [ 'query' => '((Category:Building Complex))((Part Of Site::@@@@));?Display Title;format~list;sort~Display Title;sep~,;link~none;headers~hide;limit~500' ];
	private $expected_result_parametrized_setQuery = "[[Category:Building Complex]][[Part Of Site::@@@@]];?Display Title;format=list;sort=Display Title;sep=,;link=none;headers=hide;limit=500";
	private $other_args_query_unparametrized = [ 'query' => '((Category:Building Complex));?Display Title;format~list;sort~Display Title;sep~,;link~none;headers~hide;limit~500' ];
	private $other_args_function_parametrized = [ 'function' => '((Category:Building Complex))((Part Of Site::@@@@));?Display Title;format~list;sort~Display Title;sep~,;link~none;headers~hide;limit~500' ];
	private $expected_result_parametrized_seFunction = '{{#[[Category:Building Complex]][[Part Of Site::@@@@]];?Display Title;format=list;sort=Display Title;sep=,;link=none;headers=hide;limit=500}}';
	private $other_args_function_unparametrized = [ 'function' => 'ask:((Category:Building Complex));?Display Title;format~list;sort~Display Title;sep~@@;link~none;headers~hide;limit~500' ];

	public function testCanConstruct() {

		$this->assertInstanceOf( '\SFS\SelectField', $this->selectField );
	}

	public function testProcessParameters_Query() {

		$this->selectField->processParameters(
			$this->other_args_query_parametrized, ""
		);
		$this->assertTrue(
			array_key_exists( "query", $this->other_args_query_parametrized )
		);
	}

	public function testProcessParameters_Function() {

		$this->selectField->processParameters(
			$this->other_args_function_parametrized, ""
		);
		$this->assertArrayHasKey(
			"function", $this->other_args_function_parametrized
		);
	}

	public function testParametrized_setQuery() {

		$this->selectField->setQuery( $this->other_args_query_parametrized );

		$this->assertEquals(
			$this->expected_result_parametrized_setQuery,
			$this->selectField->getData()['selectquery']
		);
		/*
		 * Optional Test.
		 */
		preg_match_all(
			"/[~(\(\()(\)\))]+/", $this->selectField->getData()['selectquery'],
			$was_remove
		);

		preg_match_all(
			"/[=(\[\[)(\]\])]+/", $this->selectField->getData()['selectquery'],
			$was_replaced
		);

		$this->assertTrue( count( $was_remove[0] ) == 0 );
		$this->assertTrue( count( $was_replaced[0] ) > 0 );
	}

	public function testUnparametrized_setQuery() {

		$this->selectField->setQuery( $this->other_args_query_unparametrized );

		$this->assertTrue( $this->selectField->getValues() !== null );
		$this->assertTrue( $this->selectField->hasStaticValues() );
	}

	public function testParametrized_setFunction() {

		$this->selectField->setFunction(
			$this->other_args_function_parametrized
		);
		$this->assertTrue(
			strcmp(
				$this->expected_result_parametrized_seFunction,
				$this->selectField->getData()['selectfunction']
			) == 0
		);
	}

	public function testUnparametrized_setFunction() {

		$this->selectField->setFunction(
			$this->other_args_function_unparametrized
		);

		$this->assertTrue( $this->selectField->hasStaticValues() );
	}

	public function testSetSelectIsMultiple_keyExistTrue() {
		$other_args = [ "part_of_multiple" => "bla bla bla" ];
		$this->selectField->setSelectIsMultiple( $other_args );
		$this->assertTrue( $this->selectField->getData()["selectismultiple"] );
	}

	public function testSetSelectIsMultiple_keyExistFalse() {

		$other_args = [ "Not_part_of_multiple" => "blas blas blas" ];
		$this->selectField->setSelectIsMultiple( $other_args );
		$this->assertFalse( $this->selectField->getData()["selectismultiple"] );
	}

	public function testSetSelectTemplate_correctData() {
		$input_name = "{{#[[Category:Building Complex]][[Part Of Site::@@@@]]";
		$result = "{{#";
		$this->selectField->setSelectTemplate( $input_name );

		$this->assertEquals(
			$this->selectField->getData()['selecttemplate'], $result
		);
	}

	public function testSetSelectTemplate_wrongData() {
		$input_name = "Category:Building Complex";
		$result = "";
		$this->selectField->setSelectTemplate( $input_name );

		$this->assertEquals(
			$this->selectField->getData()['selecttemplate'], $result
		);
	}

	public function testSetSelectField_correctData() {
		$input_name = "{{#[[Category:Building Complex]][[Part Of Site::@@@@]]";
		$result = "Part Of Site::@@@@]";

		$this->selectField->setSelectField( $input_name );

		$this->assertEquals(
			$this->selectField->getData()['selectfield'], $result
		);
	}

	public function testSetSelectField_wrongData() {
		$input_name = "Category:Building Complex";
		$result = "";
		$this->selectField->setSelectField( $input_name );

		$this->assertNotEquals(
			$this->selectField->getData()['selectfield'], $result
		);
	}

	public function testSetValueTemplate_containsMselectTemplate() {
		$input_name = "{{#[[Category:Building Complex]][[Part Of Site::@@@@]]";
		$other_args = [ "sametemplate" => "test values" ];
		$result = "{{#";
		$this->selectField->setSelectTemplate( $input_name );
		$this->selectField->setValueTemplate( $other_args );

		$this->assertEquals(
			$this->selectField->getData()["valuetemplate"], $result
		);
	}

	public function testSetValueTemplate_containsOtherArgsTemplate() {

		$other_args = [ "template" => "test values" ];

		$this->selectField->setValueTemplate( $other_args );

		$this->assertEquals(
			$this->selectField->getData()["valuetemplate"],
			$other_args["template"]
		);
	}

	public function testSetValueField() {
		$other_args = [ "field" => "test values Field" ];

		$this->selectField->setValueField( $other_args );

		$this->assertEquals(
			$this->selectField->getData()["valuefield"], $other_args["field"]
		);
	}

	public function testSetSelectRemove_keyExistTrue() {
		$other_args = [ 'rmdiv' => "Test data" ];
		$this->selectField->setSelectRemove( $other_args );
		$this->assertTrue( $this->selectField->getData()["selectrm"] );
	}

	public function testSetSelectRemove_keyExistFalse() {

		$other_args = [ "no_rmdiv" => "test data" ];
		$this->selectField->setSelectRemove( $other_args );
		$this->assertFalse( $this->selectField->getData()["selectrm"] );
	}

	public function testSetLabel_keyExistTrue() {
		$other_args = [ 'label' => "Test data" ];
		$this->selectField->setLabel( $other_args );
		$this->assertTrue( $this->selectField->getData()["label"] );
	}

	public function testSetLabel_keyExistFalse() {

		$other_args = [ "no_label" => "test data" ];
		$this->selectField->setLabel( $other_args );
		$this->assertArrayHasKey( "label", $this->selectField->getData() );
		$this->assertFalse( $this->selectField->getData()["label"] );
	}

	public function testSetDelimiter_keyExistTrue() {
		$other_args = [ "delimiter" => ":" ];
		$this->selectField->setDelimiter( $other_args );
		$this->assertEquals(
			$this->selectField->getDelimiter(), $other_args["delimiter"]
		);
		$this->assertEquals(
			$this->selectField->getData()["sep"], $other_args["delimiter"]
		);
	}

	public function testSetWgPageFormsListSeparator_keyExistTrue() {

		$g_args = [ "delimiter" => ";" ];
		$this->selectField->setDelimiter( $g_args );
		$this->assertEquals(
			$this->selectField->getDelimiter(), $g_args["delimiter"]
		);
		$this->assertEquals(
			$this->selectField->getData()["sep"], $g_args["delimiter"]
		);
	}

	protected function setUp(): void {
		parent::setUp();
		$user = $this->getMockBuilder( 'MediaWiki\User\UserIdentity' )
			->disableOriginalConstructor()
			->getMock();
		$name = $user->getName();
		$parserOption;

		if ( version_compare( MW_VERSION, '1.39', '>=' ) ) {
			//check if version is higher than 1.39, or the same (the getOption() function within ParserOptions is different then in MW 1.35)
			$parserOption = new ParserOptions( $user );
		} else {
			//if MW version is lower than 1.39
			$parserOption = new ParserOptions( $name );
		}
		$parser = MediaWikiServices::getInstance()->getParser();
		$parser->setOutputType(Parser::OT_HTML);
		$parser->setTitle( Title::newFromText( 'NO TITLE' ) );
		$parser->setOptions($parserOption);
		$parser->resetOutput();
		$parser->clearState();
		$this->selectField = new SelectField( $parser );
	}

	protected function tearDown(): void {
		unset( $this->selectField );
		parent::tearDown();
	}


}
