<?php

namespace SFS;

use SMWQueryProcessor as QueryProcessor;
use Parser;
use PFFormInput;
use MWDebug;

/**
 * @license GNU GPL v2+
 * @since 1.3
 *
 * @author Jason Zhang
 * @author Toni Hermoso Pulido
 * @author Alexander Gesinn
 */
class SemanticFormsSelectInput extends PFFormInput {

	/**
	 * Internal data container
	 *
	 * @var array
	 */
	private static $data = array();

	private $mSelectField;

	public function __construct( $inputNumber, $curValue, $inputName, $disabled, $otherArgs ) {
		parent::__construct( $inputNumber, $curValue, $inputName, $disabled, $otherArgs );

		// SelectField is a simple value object - we accept creating it in the constructor
		$this->mSelectField = new SelectField();
	}

	public static function getName() {
		return 'SF_Select';
	}

	public static function getParameters() {
		$params = parent::getParameters();
		return $params;
	}

	public function getResourceModuleNames() {
		/**
		 * Loading modules this way currently fails with:
		 * "mw.loader.state({"ext.sf_select.scriptselect":"loading"});"
		 */

		return array(
			'ext.sf_select.scriptselect'
		);
	}

	/**
	 * Returns the HTML code to be included in the output page for this input.
	 * This is currently just a wrapper for getHTML().
	 */
	public function getHtmlText() {
		return self::getHTML( $this->mCurrentValue, $this->mInputName, $this->mIsMandatory, $this->mIsDisabled,
			$this->mOtherArgs );
	}

	/**
	 * Returns the HTML code to be included in the output page for this input.
	 * @deprecated use getHtmlText() instead
	 *
	 * @param    string $cur_value A single value or a list of values with separator
	 * @param    string $input_name Name of the input including the template, e.g. Building[Part Of Site]
	 * @param            $is_mandatory
	 * @param            $is_disabled
	 * @param            $other_args
	 * @return string
	 */
	public function getHTML( $cur_value = "", $input_name = "", $is_mandatory, $is_disabled, $other_args ) {
		global # $wgScriptSelectCount,
		$sfgFieldNum, $wgUser, $wgParser;

		$selectField = array();
		// $values = null;
		$data = array();

		if ( array_key_exists( "query", $other_args ) ) {
			$this->mSelectField->setQuery( $other_args );
		} elseif ( array_key_exists( "function", $other_args ) ) {
			$this->mSelectField->setFunction( $other_args );
		}

		if ( $this->mSelectField->hasStaticValues() ) {
//			$values = explode( ",", $this->mSelectField->getValues() );
//			$values = array_map( "trim", $values );
//			$values = array_unique( $values );

//			$values = $this->mSelectField->getValues();
		} else {

//			if ( $wgScriptSelectCount == 0 ) {
			// this has been moved to getResourceModuleNames()
			//Output::addModule( 'ext.sf_select.scriptselect' );
//			}
			// $wgScriptSelectCount ++;

			$data["selectismultiple"] = array_key_exists( "part_of_multiple", $other_args );
			$this->mSelectField->setSelectIsMultiple( $other_args );

			$index = strpos( $input_name, "[" );
			$data['selecttemplate'] = substr( $input_name, 0, $index );
			$this->mSelectField->setSelectTemplate( $input_name );

			// Does this work for multiple template?
			$index = strrpos( $input_name, "[" );
			$data['selectfield'] = substr( $input_name, $index + 1, strlen( $input_name ) - $index - 2 );
			$this->mSelectField->setSelectField( $input_name );

			$valueField = array();
			$data["valuetemplate"] =
				array_key_exists( "sametemplate", $other_args ) ? $data['selecttemplate'] : $other_args["template"];

			$data["valuefield"] = $other_args["field"];

			$data['selectrm'] = array_key_exists( 'rmdiv', $other_args );
			$data['label'] = array_key_exists( 'label', $other_args );

			// TODO: use 'delimiter'?
			$data['sep'] = array_key_exists( 'sep', $other_args ) ? $other_args["sep"] : ',';

			// TODO: use SelectField class (data already contains case specific parameters)
			if ( array_key_exists( "query", $selectField ) ) {
				$data['selectquery'] = $selectField['query'];
			} else {
				$data['selectfunction'] = $selectField['function'];
			}

			self::$data[] = $data;
		}

		$extraatt = "";
		$is_list = false;

		// TODO This needs clean-up

		if ( array_key_exists( 'is_list', $other_args ) && $other_args['is_list'] == true ) {
			$is_list = true;
		}

		if ( $is_list ) {
			$extraatt = ' multiple="multiple" ';
		}

		if ( array_key_exists( "size", $other_args ) ) {
			$extraatt .= " size=\"{$other_args['size']}\"";
		}

		$classes = array();
		if ( $is_mandatory ) {
			$classes[] = "mandatoryField";
		}
		if ( array_key_exists( "class", $other_args ) ) {
			$classes[] = $other_args['class'];
		}
		if ( $classes ) {
			$cstr = implode( " ", $classes );
			$extraatt .= " class=\"$cstr\"";
		}

		$inname = $input_name;
		if ( $is_list ) {
			$inname .= '[]';
		}

		// TODO Use Html::

		$spanextra = $is_mandatory ? 'mandatoryFieldSpan' : '';
		$ret = "<span class=\"inputSpan $spanextra\"><select name='$inname' id='input_$sfgFieldNum' $extraatt>";
		$curvalues = null;
		if ( $cur_value ) {
			if ( $cur_value === 'current user' ) {
				$cur_value = $wgUser->getName();
			}
			if ( is_array( $cur_value ) ) {
				$curvalues = $cur_value;
			} else {
				// TODO: this needs to take the separator into account (could be something else than ',')
				$curvalues = array_map( "trim", explode( ",", $cur_value ) );
			}

		} else {
			$curvalues = array();
		}

		// TODO handle empty value case.
		$ret .= "<option></option>";

		foreach ( $curvalues as $cur ) {
			$ret .= "<option selected='selected'>$cur</option>";
		}

		if ( $this->mSelectField->hasStaticValues() ) {
			foreach ( $this->mSelectField->getValues() as $val ) {
				if ( !in_array( $val, $curvalues ) ) {
					$ret .= "<option>$val</option>";
				}
			}
		}

		$ret .= "</select></span>";
		$ret .= "<span id=\"info_$sfgFieldNum\" class=\"errorMessage\"></span>";

		if ( $other_args["is_list"] ) {
			$hiddenname = $input_name . '[is_list]';
			$ret .= "<input type='hidden' name='$hiddenname' value='1' />";
		}

		if ( !$staticvalue ) {
			$item = Output::addToHeadItem( $data );
			//$wgOut->addJsConfigVars('sf_select', array(json_encode( $data )));
		}

		Output::commitToParserOutput();
		return $ret;
	}
}
