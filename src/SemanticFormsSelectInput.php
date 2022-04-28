<?php

/**
 * @license GNU GPL v2+
 * @since 1.3
 *
 * @author Jason Zhang
 * @author Toni Hermoso Pulido
 * @author Alexander Gesinn
 */

namespace SFS;

use MediaWiki\MediaWikiServices;
use SMWQueryProcessor as QueryProcessor;
use Parser;
use PFFormInput;
use MWDebug;

class SemanticFormsSelectInput extends PFFormInput {

	/**
	 * Internal data container
	 *
	 * @var array
	 */
	private static $data = [];

	private $mSelectField;

	public function __construct( $inputNumber, $curValue, $inputName, $disabled, $otherArgs ) {
		parent::__construct( $inputNumber, $curValue, $inputName, $disabled, $otherArgs );

		// SelectField is a simple value object - we accept creating it in the constructor
		$parser = MediaWikiServices::getInstance()->getParser();
		$this->mSelectField = new SelectField( $parser );
	}

	public static function getName() {
		return 'SF_Select';
	}

	public static function getParameters() {
		$params = parent::getParameters();
		return $params;
	}

	public function getResourceModuleNames() {
		return [ 'ext.sfs' ];
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
		global $wgPageFormsFieldNum, $wgUser;

		// shortcut to the SelectField object
		$selectField = $this->mSelectField;

		// get 'delimiter' before 'query' or 'function'
		$selectField->setDelimiter( $other_args );

		if ( array_key_exists( "query", $other_args ) ) {
			$selectField->setQuery( $other_args );
		} elseif ( array_key_exists( "function", $other_args ) ) {
			$selectField->setFunction( $other_args );
		}

		if ( array_key_exists( "label", $other_args ) ) {
			$selectField->setLabel( $other_args );
		}

		// parameters are only required if values needs to be retrieved dynamically
		if ( !$selectField->hasStaticValues() ) {
			$selectField->setSelectIsMultiple( $other_args );
			$selectField->setSelectTemplate( $input_name );
			$selectField->setSelectField( $input_name );
			$selectField->setValueTemplate( $other_args );
			$selectField->setValueField( $other_args );
			$selectField->setSelectRemove( $other_args );

			$item = Output::addToHeadItem( $selectField->getData() );
		}

		Output::commitToParserOutput();

		// prepare the html input tag

		$extraatt = "";
		$is_list = false;

		if ( array_key_exists( 'is_list', $other_args ) && $other_args['is_list'] == true ) {
			$is_list = true;
		}

		if ( $is_list ) {
			$extraatt = ' multiple="multiple" ';
		}

		if ( array_key_exists( "size", $other_args ) ) {
			$extraatt .= " size=\"{$other_args['size']}\"";
		}

		$classes = [];
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
		$is_single_select = (!$is_list) ? 'select-sfs-single' : '' ;
		$ret = "<span class=\"inputSpan select-sfs $is_single_select $spanextra\"><select name='$inname' id='input_$wgPageFormsFieldNum' $extraatt>";

		$curvalues = null;
		if ( $cur_value ) {
			if ( $cur_value === 'current user' ) {
				$cur_value = $wgUser->getName();
			}
			if ( is_array( $cur_value ) ) {
				$curvalues = $cur_value;
			} else {
				// delimiter for $cur_value is always ',' - PF seems to ignore $wgPageFormsListSeparator
				$curvalues = array_map( "trim", explode( $selectField->getDelimiter(), $cur_value ) );
			}

		} else {
			$curvalues = [];
		}


		$labelArray = [];
		if ( array_key_exists( "label", $other_args ) && $curvalues ) {
			// $labelArray = $this->getLabels( $curvalues );
		}

		// TODO handle empty value case.
		$ret .= "<option></option>";

		if ( $selectField->hasStaticValues() ) {

			$values = $selectField->getValues();

			if ( array_key_exists( "label", $other_args ) && $values ) {
				$labelArray = $this->getLabels( $values );
			}

			if ( is_array( $values ) ) {

				foreach ( $values as $val ) {

					$selected = "";

					if ( array_key_exists( $val, $labelArray ) ) {

						if ( in_array( $labelArray[ $val ][0], $curvalues ) ) {
							$selected = " selected='selected'";
						}

						$ret.="<option".$selected." value='".$labelArray[ $val ][0]."'>".$labelArray[ $val ][1]."</option>";

					} else {

						if ( in_array( $val, $curvalues ) ) {
							$selected = " selected='selected'";
						}

						$ret .= "<option".$selected.">$val</option>";
					}
				}
			}
		} else {

			foreach ( $curvalues as $cur ) {
				$selected = "";

				if ( array_key_exists( $cur, $labelArray ) ) {

					if ( in_array( $labelArray[ $cur ][0], $curvalues ) ) {
						$selected = " selected='selected'";
					}

					$ret.="<option".$selected." value='".$labelArray[ $cur ][0]."'>".$labelArray[ $cur ][1]."</option>";

				} else {
					if ( in_array( $cur, $curvalues ) ) {
						$selected = " selected='selected'";
					}
					$ret.="<option".$selected.">$cur</option>";
				}
			}

		}

		$ret .= "</select></span>";
		$ret .= "<span id=\"info_$wgPageFormsFieldNum\" class=\"errorMessage\"></span>";

		if ( $other_args["is_list"] ) {
			$hiddenname = $input_name . '[is_list]';
			$ret .= "<input type='hidden' name='$hiddenname' value='1' />";
		}

		return $ret;
	}


	private function getLabels( $labels ) {

		$labelArray = [ ];

		if ( is_array( $labels ) ) {
			foreach ( $labels as $label ) {

				$labelKey = $label;
				$labelValue = $label;

				// Tricky thing if ( ) already in name
				if ( strpos( $label, ")" ) && strpos( $label, "(" ) ) {

					// Check Break
					$openBr = 0;
					$doneBr = 0;
					$num = 0;

					$labelArr = str_split ( $label );

					$end = count( $labelArr ) - 1;
					$iter = $end;

					$endBr = $end;
					$startBr = 0;

					while ( $doneBr == 0 && $iter >= 0 ) {

						$char = $labelArr[ $iter ];

						if ( $char == ")" ) {
							$openBr = $openBr - 1;

							if ( $num == 0 ) {
								$endBr = $iter;
								$num = $num + 1;
							}
						}

						if ( $char == "(" ) {
							$openBr = $openBr + 1;

							if ( $num > 0 && $openBr == 0 ) {
								$startBr = $iter;
								$doneBr = 1;
							}
						}

						$iter = $iter - 1;

					}

					$labelValue = implode( "", array_slice( $labelArr, $startBr+1, $endBr-$startBr-1 ) );
					$labelKey = implode( "", array_slice( $labelArr, 0, $startBr-1 ) );

				}

				$labelArray[ $label ] = [ $labelKey, $labelValue ] ;
			}

		}

		return $labelArray;

	}
}
