/**
The functionality of originalValueLookup should better be defined in PF itself.
As it is only used by SFS by now, add it here for simplicity to be able to profit when
using different PF versions.
 */
(function ($, mw, pf) {

	/**
	 * Create a function to look up the original value corresponding to a (possibly disambiguated) displayed
	 * value used in a PageForms element
	 *
	 * @param {Object} element the element for which to lookup original values
	 * @return {function(string): string} a function performing the lookup of the corresponding Title in mw.config
	 */
	pf.originalValueLookup = function originalValueLookup(element) {
		const variant = Object.keys(elementVariants)
			.map(k => elementVariants[k])
			.find(v => v.condition(element));
		return variant ?
			lookupIn(variant.mappings(element)) :
			value => value;
	};

	const elementVariants = {
		radiobutton: {
			condition: e => e.parent().hasClass('radioButtonItem'),
			mappings: e => e.parents('.radioButtonSpan')
				.find('[data-original-value]')
				.map(function () {
					return {
						original: $(this).data('original-value'),
						value: $(this).attr('value')
					};
				})
				.get()
		},
		autocomplete: {
			condition: e => e.attr('autocompletesettings'),
			mappings: e => {
				const autocompletesettings = e.attr('autocompletesettings');
				const mapping = mw.config.get('wgPageFormsAutocompleteValues')[autocompletesettings];
				return Object.keys(mapping)
					.map(k => ({original: k, value: mapping[k]}));
			}
		}
	};

	function lookupIn(mappings) {
		return value => {
			const match = mappings.find(m => m.value === value);
			return match ? match.original : value;
		};
	}

}(jQuery, mediaWiki, pageforms));
