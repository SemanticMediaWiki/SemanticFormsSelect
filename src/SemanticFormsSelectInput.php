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
	 * @param    string[] $other_args Array of other field parameters
	 * @return string
	 */
	public function getHTML( $cur_value = "", $input_name = "", $is_mandatory, $is_disabled, Array $other_args ) {
		global $sfgFieldNum, $wgUser;

		// shortcut to the SelectField object
		$selectField = $this->mSelectField;

		// get 'delimiter' before 'query' or 'function'
		$selectField->setDelimiter( $other_args );

		if ( array_key_exists( "query", $other_args ) ) {
			$selectField->setQuery( $other_args );
		} elseif ( array_key_exists( "function", $other_args ) ) {
			$selectField->setFunction( $other_args );
		}

		// parameters are only required if values needs to be retrieved dynamically
		if ( !$selectField->hasStaticValues() ) {
			$selectField->setSelectIsMultiple( $other_args );
			$selectField->setSelectTemplate( $input_name );
			$selectField->setSelectField( $input_name );
			$selectField->setValueTemplate( $other_args );
			$selectField->setValueField( $other_args );
			$selectField->setSelectRemove( $other_args );
			$selectField->setLabel( $other_args );

			$item = Output::addToHeadItem( $selectField->getData() );
		}

		Output::commitToParserOutput();

		// prepare the html input tag

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
				// delimiter for $cur_value is always ',' - PF seems to ignore $wgPageFormsListSeparator
				$curvalues = array_map( "trim", explode( ',', $cur_value ) );
			}

		} else {
			$curvalues = array();
		}

		// TODO handle empty value case.
		$ret .= "<option></option>";

		foreach ( $curvalues as $cur ) {
			$ret .= "<option selected='selected'>$cur</option>";
		}

		if ( $selectField->hasStaticValues() ) {
			foreach ( $selectField->getValues() as $val ) {
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

		return $ret;
	}
}
