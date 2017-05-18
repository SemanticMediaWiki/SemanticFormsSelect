<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SFS\Tests;

use       SFS\SelectField;

use Parser;
use ParserOptions;
use ParserOutput;
use Title;

/**
 * @covers \SFS\SelectField
 * @group  semantic-forms-select
 * @author Felix
 */
class SelectFieldTest extends \PHPUnit_Framework_TestCase {
	private $SelectField;
	private $parser;
	// Defined variables
	private $other_args_query_parametrized = [ 'query' => '((Category:Building Complex))((Part Of Site::@@@@));?Display Title;format~list;sort~Display Title;sep~,;link~none;headers~hide;limit~500' ];
	private $expected_result_parametrized_setQuery = "[[Category:Building Complex]][[Part Of Site::@@@@]];?Display Title;format=list;sort=Display Title;sep=,;link=none;headers=hide;limit=500";
	private $other_args_query_unparametrized = [ 'query' => '((Category:Building Complex));?Display Title;format~list;sort~Display Title;sep~,;link~none;headers~hide;limit~500' ];
	private $other_args_function_parametrized = [ 'function' => '((Category:Building Complex))((Part Of Site::@@@@));?Display Title;format~list;sort~Display Title;sep~,;link~none;headers~hide;limit~500' ];
	private $expected_result_parametrized_seFunction = '{{#[[Category:Building Complex]][[Part Of Site::@@@@]];?Display Title;format=list;sort=Display Title;sep=,;link=none;headers=hide;limit=500}}';
	private $other_args_function_unparametrized = [ 'function' => 'ask:((Category:Building Complex));?Display Title;format~list;sort~Display Title;sep~@@;link~none;headers~hide;limit~500' ];
	private $expected_result_unparametrized_seFunction = "Building Complex:86543eab-4112-4616-be50-17dcdc24c346 (OFD.AEXH)@@Building Complex:5b9e26f8-6c57-48ff-a6b8-42a4e50fe472 (OFD.AEXH)@@Building Complex:93b076aa-cbe9-4371-8b61-c17c26f1872f (OFD.AMEXH)@@Building Complex:59577450-1582-4d6e-9621-3ac0531a728e (OFD.EEXH)@@Building Complex:1a9bed0b-67de-4e71-8528-f2b6a8907814 (RContiAve.Sport Complex)@@Building Complex:6a2242ea-7536-4a6d-85d2-f2ba4398ef44 (TB.BC)@@Building Complex:2db51fb1-10b6-4d4c-a152-f512914781ff (TB.BD)";

	public function testCanConstruct() {

		$this->assertInstanceOf( '\SFS\SelectField', $this->SelectField );
	}

	public function testProcessParameters_Query() {

		$this->SelectField->processParameters(
			"", $this->other_args_query_parametrized
		);
		$this->assertTrue(
			array_key_exists( "query", $this->other_args_query_parametrized )
		);
	}

	public function testProcessParameters_Function() {

		$this->SelectField->processParameters(
			"", $this->other_args_function_parametrized
		);
		$this->assertArrayHasKey(
			"function", $this->other_args_function_parametrized
		);
	}

	public function testParametrized_setQuery() {

		$this->SelectField->setQuery( $this->other_args_query_parametrized );

		$this->assertEquals(
			$this->expected_result_parametrized_setQuery,
			$this->SelectField->getData()['selectquery']
		);
		/*
		 * Optional Test.
		 */
		preg_match_all(
			"/[~(\(\()(\)\))]+/", $this->SelectField->getData()['selectquery'],
			$was_remove
		);

		preg_match_all(
			"/[=(\[\[)(\]\])]+/", $this->SelectField->getData()['selectquery'],
			$was_replaced
		);

		$this->assertTrue( count( $was_remove[0] ) == 0 );
		$this->assertTrue( count( $was_replaced[0] ) > 0 );
	}

	public function testUnparametrized_setQuery() {

		$this->SelectField->setQuery( $this->other_args_query_unparametrized );

		$this->assertTrue( $this->SelectField->getValues() !== null );
		$this->assertTrue( $this->SelectField->hasStaticValues() );
	}

	public function testParametrized_setFunction() {

		$this->SelectField->setFunction(
			$this->other_args_function_parametrized
		);
		$this->assertTrue(
			strcmp(
				$this->expected_result_parametrized_seFunction,
				$this->SelectField->getData()['selectfunction']
			) == 0
		);
	}

	public function testUnparametrized_setFunction() {

		$this->SelectField->setFunction(
			$this->other_args_function_unparametrized
		);

		$this->assertTrue( $this->SelectField->hasStaticValues() );
	}

	public function testSetSelectIsMultiple_keyExistTrue() {
		$other_args = array( "part_of_multiple" => "bla bla bla" );
		$this->SelectField->setSelectIsMultiple( $other_args );
		$this->assertTrue( $this->SelectField->getData()["selectismultiple"] );
	}

	public function testSetSelectIsMultiple_keyExistFalse() {

		$other_args = array( "Not_part_of_multiple" => "blas blas blas" );
		$this->SelectField->setSelectIsMultiple( $other_args );
		$this->assertFalse( $this->SelectField->getData()["selectismultiple"] );
	}

	public function testSetSelectTemplate_correctData() {
		$input_name = "{{#[[Category:Building Complex]][[Part Of Site::@@@@]]";
		$result = "{{#";
		$this->SelectField->setSelectTemplate( $input_name );

		$this->assertEquals(
			$this->SelectField->getData()['selecttemplate'], $result
		);
	}

	public function testSetSelectTemplate_wrongData() {
		$input_name = "Category:Building Complex";
		$result = "";
		$this->SelectField->setSelectTemplate( $input_name );

		$this->assertEquals(
			$this->SelectField->getData()['selecttemplate'], $result
		);
	}

	public function testSetSelectField_correctData() {
		$input_name = "{{#[[Category:Building Complex]][[Part Of Site::@@@@]]";
		$result = "Part Of Site::@@@@]";

		$this->SelectField->setSelectField( $input_name );

		$this->assertEquals(
			$this->SelectField->getData()['selectfield'], $result
		);
	}

	public function testSetSelectField_wrongData() {
		$input_name = "Category:Building Complex";
		$result = "";
		$this->SelectField->setSelectField( $input_name );

		$this->assertNotEquals(
			$this->SelectField->getData()['selectfield'], $result
		);
	}

	public function testSetValueTemplate_containsMselectTemplate() {
		$input_name = "{{#[[Category:Building Complex]][[Part Of Site::@@@@]]";
		$other_args = [ "sametemplate" => "test values" ];
		$result = "{{#";
		$this->SelectField->setSelectTemplate( $input_name );
		$this->SelectField->setValueTemplate( $other_args );

		$this->assertEquals(
			$this->SelectField->getData()["valuetemplate"], $result
		);
	}

	public function testSetValueTemplate_containsOtherArgsTemplate() {

		$other_args = [ "template" => "test values" ];

		$this->SelectField->setValueTemplate( $other_args );

		$this->assertEquals(
			$this->SelectField->getData()["valuetemplate"],
			$other_args["template"]
		);
	}

	public function testSetValueField() {
		$other_args = [ "field" => "test values Field" ];

		$this->SelectField->setValueField( $other_args );

		$this->assertEquals(
			$this->SelectField->getData()["valuefield"], $other_args["field"]
		);
	}

	public function testSetSelectRemove_keyExistTrue() {
		$other_args = array( 'rmdiv' => "Test data" );
		$this->SelectField->setSelectRemove( $other_args );
		$this->assertTrue( $this->SelectField->getData()["selectrm"] );
	}

	public function testSetSelectRemove_keyExistFalse() {

		$other_args = array( "no_rmdiv" => "test data" );
		$this->SelectField->setSelectRemove( $other_args );
		$this->assertFalse( $this->SelectField->getData()["selectrm"] );
	}

	public function testSetLabel_keyExistTrue() {
		$other_args = array( 'label' => "Test data" );
		$this->SelectField->setLabel( $other_args );
		$this->assertTrue( $this->SelectField->getData()["label"] );
	}

	public function testSetLabel_keyExistFalse() {

		$other_args = array( "no_label" => "test data" );
		$this->SelectField->setLabel( $other_args );
		$this->assertArrayHasKey( "label", $this->SelectField->getData() );
		$this->assertFalse( $this->SelectField->getData()["label"] );
	}

	public function testSetDelimiter_keyExistTrue() {
		$other_args = array( "delimiter" => ":" );
		$this->SelectField->setDelimiter( $other_args );
		$this->assertEquals(
			$this->SelectField->getDelimiter(), $other_args["delimiter"]
		);
		$this->assertEquals(
			$this->SelectField->getData()["sep"], $other_args["delimiter"]
		);
	}

	public function testSetWgPageFormsListSeparator_keyExistTrue() {

		$g_args = array( "Global_delimiter" => ";" );
		$this->SelectField->setDelimiter( $g_args );
		$this->assertEquals(
			$this->SelectField->getDelimiter(), $g_args["Global_delimiter"]
		);
		$this->assertEquals(
			$this->SelectField->getData()["sep"], $g_args["Global_delimiter"]
		);
	}

	protected function setUp() {
		parent::setUp();
		$this->parser = $GLOBALS['wgParser'];
		$this->parser->setTitle( Title::newFromText( 'NO TITLE' ) );
		$this->parser->mOptions = new ParserOptions();
		$this->parser->mOutput = new ParserOutput(
		);  // Stored result thats passed back to Parser Object
		$this->parser->clearState();
		$this->SelectField = new SelectField( $this->parser );
	}

	protected function tearDown() {
		unset( $this->SelectField );
		parent::tearDown();
	}


}