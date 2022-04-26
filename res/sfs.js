window.semanticformsselect = function ($, mw, pf) {
	'use strict';

	function initialize() {
		$(document).ready(() => {
			/**
			 * valuetemplate: string,
			 * valuefield: string, value is the form field on which other select element depends on. change
			 *  on this field will trigger a load event for selectfield.
			 * selecttemplate: string
			 * selectfield: string
			 * selectismultiple: boolean, Whether this template is a multiple template.
			 * selectquery or selectfunction: the query ot function to execute
			 * selectrm: boolean remove the div if the selected value for a field is not valid any more.
			 * label: boolean, process ending content () as label in option values.
			 * sep: Separator for the list of retrieved values, default ','
			 */
			const sfsObjects = getSfsObjects();

			registerChangeHandlers(sfsObjects);
			populateSelectFields(sfsObjects);
		});
	}

	function registerChangeHandlers(sfsObjects) {
		$("form#pfForm").change(function (event) {
			handleChange(event.target, sfsObjects);
		});
	}

	function populateSelectFields(sfsObjects) {
		for (let i = 0; i < sfsObjects.length; i++) {
			const sfsObject = sfsObjects[i];
			const objs =
				// support multi instance templates: select all "input" items starting with sfsObject.valuetemplate
				// and containing sfsObject.valuefield
				$('[name^="' + sfsObject.valuetemplate + '"][name*="' + sfsObject.valuefield + '"]')
					// but skip the hidden templates
					.not('input[name*=map_field]');

			objs.trigger("change");
		}
	}

	/**
	 * Read SFS Objects from mw.config and eliminates duplicates
	 * TODO: Eliminate duplicates already on server?
	 **/
	function getSfsObjects() {
		const objects = JSON.parse(mw.config.get('sf_select'));
		const distinctObjects = [];

		for (let i = 0; i < objects.length; i++) {
			let found = false;
			const of = objects[i];
			if (!of.selectismultiple) {
				distinctObjects.push(of);
				continue;
			}
			for (let j = 0; j < distinctObjects.length; j++) {
				const nf = distinctObjects[j];
				if (of.selecttemplate === nf.selecttemplate && of.selectfield === nf.selectfield) {
					found = true;
					break;
				}
			}
			if (!found) {
				distinctObjects.push(of);
			}
		}
		return distinctObjects;
	}

	/**
	 * @param src
	 * @param sfsObjects
	 */
	function handleChange(src, sfsObjects) {
		if (src.tagName.toLowerCase() !== 'select' && src.tagName.toLowerCase() !== 'input') {
			return;
		}

		let v = [];
		const selectElement = $(src);
		let name = src.name;
		let selectedValue = selectElement.val();

		if (selectedValue) {
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
		const srcName = parseFieldIdentifier(name);

		for (let i = 0; i < sfsObjects.length; i++) {
			if (sfsObjects[i].hasOwnProperty("staticvalue") && sfsObjects[i].staticvalue) {
				changeSelected(sfsObjects[i], srcName);
			} else {
				executeQuery(sfsObjects[i], srcName, v);
			}
		}
	}

	/**
	 * Uses pf.originalValueLookup to lookup original values from displayed values in the context
	 * of the given element; returns the identity function otherwise
	 *
	 * @param element
	 * @returns (function(*): *)
	 */
	function originalValueLookup(element) {
		// Use the real originalValueLookup if PF supports it
		return pf.originalValueLookup
			? pf.originalValueLookup(element)
			: value => value;
	}

	/**
	 * Parses a string of the form "TEMPLATE[INDEX][PROPERTY]" to an object
	 * { template: "TEMPLATE", index: "INDEX", isList: false, property: "PROPERTY" }
	 *
	 * @param name
	 * @returns {{index: string, isList: boolean, property: string, template: string}}
	 */
	function parseFieldIdentifier(name) {
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

	function changeSelected(sfsObject, nameobj) {
		const selectPat = getSelectFieldPat(nameobj, sfsObject);
		$(selectPat).each(function (index, element) {
			//keep selected values;
			let selectedValues = $(element).val();

			if (!selectedValues && sfsObject.hasOwnProperty("curvalues")) {
				selectedValues = sfsObject.curvalues;
			}

			if (!selectedValues) {
				selectedValues = [];
			} else if (!$.isArray(selectedValues)) {
				selectedValues = [selectedValues];
			}

			if (element.options && element.options.length > 0) {

				const options = $.map(element.options, function (option) {
					return option.value;
				});

				for (let c = 0; c < selectedValues.length; c++) {
					if ($.inArray(selectedValues[c], options)) {
						const changed = $(element).attr("data-changed");
						if (changed) {
							$(element).val(selectedValues[c]).trigger('change');
						}
					}
				}
			}
		});
	}

	function executeQuery(sfsObject, srcName, v) {
		if (srcName.template === sfsObject.valuetemplate && srcName.property === sfsObject.valuefield) {
			//good, we have a match.
			if (v.length === 0 || v[0] === '') {
				// No values
				setDependentValues(srcName, sfsObject, []);
			} else {
				// Values
				const param = {};
				param['action'] = 'sformsselect';
				param['format'] = 'json';
				param['sep'] = sfsObject.sep;

				if (sfsObject.selectquery) {
					param['query'] = sfsObject.selectquery.replace("@@@@", v.join('||'));
					param['approach'] = 'smw';

				} else {
					param['query'] = sfsObject.selectfunction.replace("@@@@", v.join(","));
					param['approach'] = 'function';
				}

				const posting = $.get(mw.config.get('wgScriptPath') + "/api.php", param);
				posting.done(function (data) {
					// Let's pass values
					setDependentValues(srcName, sfsObject, data["sformsselect"].values);
				}).fail(function () {
					console.log("Error!");
				});
			}
		}
	}

	function setDependentValues(nameobj, sfsObject, values) {
		const selectPat = getSelectFieldPat(nameobj, sfsObject);

		$(selectPat).each(function (index, element) {
			// keep selected values;
			let selectedValues = jQuery(element).val();

			if (!selectedValues && sfsObject.hasOwnProperty("curvalues")) {
				selectedValues = sfsObject.curvalues;
			}

			if (!selectedValues) {
				selectedValues = [];
			} else if (!$.isArray(selectedValues)) {
				selectedValues = [selectedValues];
			}

			element.options.length = values.length;
			const newselected = [];

			if (sfsObject.label) {
				const namevalues = parsePlainlistQueryResult(values);
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
				if (sfsObject.selectrm && sfsObject.selecttemplate !== sfsObject.valuetemplate && sfsObject.selectismultiple) {
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

	/**
	 * Parse the query result of the api call (in plaintext format)
	 *
	 * @param values
	 * @returns array of two element arrays containing [title, property]
	 */
	function parsePlainlistQueryResult(values) {
		return values.map(function (value) {
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

	return {
		initialize: initialize,

		// Exporting the following functions here only serves testing purposes:
		private: {
			parseFieldIdentifier: parseFieldIdentifier,
			originalValueLookup: originalValueLookup,
			parsePlainlistQueryResult: parsePlainlistQueryResult
		}
	};
};
