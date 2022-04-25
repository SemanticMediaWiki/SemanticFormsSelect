( function ($, mw) {
	'use strict';

	/*
	 * return a jquery pattern string to find out form fields which values will be changed
	 */
	function getSelectFieldPat(nameObj, f) {
		let selectpat;
		if (f.selectismultiple) {
			if (f.selecttemplate === f.valuetemplate) {
				// each select field in a multiple
				// template depends on its value field.
				const pat = "select[name='" + f.selecttemplate + "[" + nameObj.index + "][" + f.selectfield + "]']";
				const pat1 = "select[name='" + f.selecttemplate + "[" + nameObj.index + "][" + f.selectfield + "][]']";
				selectpat = pat + "," + pat1;
			} else {
				// multiple select fields depends on one
				// value field.
				selectpat = "select[name^='"
					+ f.selecttemplate
					+ "'][name$='["
					+ f.selectfield
					+ "]'], select[name^='"
					+ f.selecttemplate
					+ "'][name$='["
					+ f.selectfield + "][]']";
			}
		} else {
			selectpat = "select[name='"
				+ f.selecttemplate + "["
				+ f.selectfield
				+ "]'], select[name='"
				+ f.selecttemplate + "["
				+ f.selectfield + "][]']";
		}
		return selectpat;
	}

	/*
	 * Parse the SF field name into an objetc for easy process
	 */
	function parseName(name) {
		const names = name.split('[');
		const nameObj = {template: names[0]};
		if (names[names.length - 1] === ']') {
			nameObj.isList = true;
			let property = names[names.length - 2]
			property = property.substr(0, property.length - 1);
			nameObj.property = property;
			if (names.length === 4) {
				let index = names[1];
				index = index.substr(0, index.length - 1);
				nameObj.index = index;
			} else {
				nameObj.index = null;
			}
		} else {
			nameObj.isList = false;
			let property = names[names.length - 1]
			property = property.substr(0, property.length - 1);
			nameObj.property = property;
			if (names.length === 3) {
				let index = names[1];
				index = index.substr(0, index.length - 1);
				nameObj.index = index;
			} else {
				nameObj.index = null;
			}
		}
		return nameObj;
	}

	function setDependentValues(nameobj, fobj, values) {

		const selectPat = getSelectFieldPat(nameobj, fobj);

		$(selectPat).each(function (index, element) {
			// keep selected values;
			let selectedValues = jQuery(element).val();

			if ( !selectedValues && fobj.hasOwnProperty("curvalues") ) {
				selectedValues = fobj.curvalues;
			}

			if (!selectedValues) {
				selectedValues = [];
			} else if (!$.isArray(selectedValues)) {
				selectedValues = [selectedValues];
			}

			element.options.length = values.length;
			const newselected = [];

			if (fobj.label) {
				const namevalues = processNameValues(values);
				for (let i = 0; i < namevalues.length; i++) {
					element.options[i] = new Option(namevalues[i][1], namevalues[i][0]);
					if ($.inArray(namevalues[i][0], selectedValues) !== -1) {
						element.options[i].selected = true;
						newselected.push(namevalues[i][0]);
					}
				}
			} else {
				for (let i = 0; i < values.length; i++) {
					element.options[i] = new Option(values[i]);
					if ($.inArray(values[i], selectedValues) !== -1) {
						element.options[i].selected = true;
						newselected.push(values[i]);
					}
				}
			}

			if (newselected.length === 0) {
				if (fobj.selectrm && fobj.selecttemplate !== fobj.valuetemplate && fobj.selectismultiple) {
					$(element).closest("div.multipleTemplateInstance").remove();
				} else {
					if (selectedValues.length !== 0 || values.length === 1)
						$(element).trigger("change");
				}
			} else if (!arrayEqual(newselected, selectedValues)) {
				$(element).trigger("change");
			}
		});
	}

	/** Function for turning name values from 'Page (property)' results **/
	function processNameValues( values ) {
		return values.map(function(value) {
			value = value || '';
			const cutAt = value.lastIndexOf('(');
			return cutAt === -1
				? [value, value]
				: [value.substring(0, cutAt).trim(),
					value.substring(cutAt + 1, value.length - 1)
				];
		});
	}

	function arrayEqual(a, b) {
		if (a.length !== b.length)
			return false;
		a = a.sort();
		b = b.sort();
		for (let i = 0; i < a.length; i++) {
			if (a[i] !== b[i])
				return false;
		}
		return true;
	}

	// Use the real originalValueLookup if PF supports it
	const originalValueLookup = pf.originalValueLookup || (() => value => value);

	/**
	 * valuetemplate:string,
	 * valuefield:string, value is the form field on which other select element depends on. change
	 *  on this field will trigger a load event for selectfield.
	 * selecttemplate:string
	 * selectfield:string
	 * selectismultiple:boolean, Whether this template is a multiple template.
	 * selectquery or selectfunciton: the query ot function to execute
	 * selectrm:boolean remove the div if the selected value for a field is not valid any more.
	 * label: boolean, process ending content () as label in option values.
	 * sep: Separator for the list of retrieved values, default ','
	 */
	let sfsObjects = JSON.parse(mw.config.get('sf_select'));

	/**
	 * changeHandler
	 * @param src
	 */
	function changeHandler(src) {
		if (src.tagName.toLowerCase() !== 'select' && src.tagName.toLowerCase() !== 'input') {
			return;
		}

		let v = [];
		const selectElement = $(src);
		let name = src.name;
		let selectedValue = selectElement.val();

		if ( selectedValue ) {
			if ($.isArray(selectedValue)) {
				v = selectedValue;
			} else {
				if (selectElement.attr('type') === "checkbox") {
					v = (selectElement.is(":checked")) ? ["true"] : ["false"];
					// cut off [value] component from name
					name = src.name.substr(0, src.name.indexOf("[value]"));
				} else {
					//split and trim
					v = $.map(selectedValue.split(";"), $.trim);
				}
			}
		}

		const lookupOriginalValue = originalValueLookup(selectElement);
		v = v.map(lookupOriginalValue);
		const srcName = parseName(name);

		for (let i = 0; i < sfsObjects.length; i++) {
			if ( sfsObjects[i].hasOwnProperty("staticvalue") && sfsObjects[i].staticvalue ) {
				changeSelected( sfsObjects[i], srcName );
			} else {
				prepareQuery( sfsObjects[i], srcName, v );
			}
		}
	}

	function changeSelected( fobj, nameobj ) {

		const selectPat = getSelectFieldPat(nameobj, fobj);

		$(selectPat).each(function(index, element){
			//keep selected values;
			let selectedValues = $(element).val();

			if ( !selectedValues && fobj.hasOwnProperty("curvalues") ) {
				selectedValues = fobj.curvalues;
			}

			if (!selectedValues){
				selectedValues=[];
			} else if (!$.isArray(selectedValues)){
				selectedValues=[selectedValues];
			}

			if ( element.options && element.options.length > 0 ) {

				const options = $.map(element.options, function (option) {
					return option.value;
				});

				for (let c = 0; c < selectedValues.length; c++ ) {
					if ( $.inArray( selectedValues[c], options ) ) {
						const changed = $(element).attr("data-changed");
						if ( changed ) {
							$( element ).val( selectedValues[c] ).trigger('change');
						}
					}
				}
			}
		});
	}

	/**
	 * prepareQuery
	 */
	function prepareQuery(fobj, srcName, v) {
		if (srcName.template === fobj.valuetemplate && srcName.property === fobj.valuefield) {
			//good, we have a match.
			if (v.length === 0 || v[0] === '') {
				// No values
				setDependentValues(srcName, fobj, []);
			} else {
				// Values
				const param = {};
				param['action'] = 'sformsselect';
				param['format'] = 'json';
				param['sep'] = fobj.sep;

				if (fobj.selectquery) {
					param['query'] = fobj.selectquery.replace("@@@@", v.join('||'));
					param['approach'] = 'smw';

				} else {
					param['query'] = fobj.selectfunction.replace("@@@@", v.join(","));
					param['approach'] = 'function';
				}

				const posting = $.get(mw.config.get('wgScriptPath') + "/api.php", param);
				posting.done(function (data) {
					// Let's pass values
					setDependentValues(srcName, fobj, data["sformsselect"].values);
				}).fail(function () {
					console.log("Error!");
				});

				// break; // Avoid loading fobj again
			}
		}
	}

	/**
	 * removeDuplicateFobjs
	 * SF form add a fobj for each field in a multiple template.
	 * In reality we only need a fobj to reduce the ajax call.
	 **/
	function removeDuplicateFobjs(objects) {
		const newfobjs = [];

		for (let i = 0; i < objects.length; i++) {
			let found = false;
			const of = objects[i];
			if (!of.selectismultiple) {
				newfobjs.push(of);
				continue;
			}
			for (let j = 0; j < newfobjs.length; j++) {
				const nf = newfobjs[j];
				if (of.selecttemplate === nf.selecttemplate && of.selectfield === nf.selectfield) {
					found = true;
					break;
				}
			}
			if (!found) {
				newfobjs.push(of);
			}
		}

		return newfobjs;
	}

	//simplify duplicated object.
	sfsObjects = removeDuplicateFobjs(sfsObjects);

	// register change handler
	$(document).ready(() => {
		$("form#pfForm").change(function (event) {
			changeHandler(event.target);
		});

		// populate Select fields at load time
		for (let i = 0; i < sfsObjects.length; i++) {

			const fobj = sfsObjects[i];
			const objs =
				// support multi instance templates: select all "input" items starting with fobj.valuetemplate
				// and containing fobj.valuefield
				$('[name^="' + fobj.valuetemplate + '"][name*="' + fobj.valuefield + '"]')
					// but skip the hidden templates
					.not('input[name*=map_field]');

			objs.trigger("change");
		}
	});
}(jQuery, mediaWiki) );
