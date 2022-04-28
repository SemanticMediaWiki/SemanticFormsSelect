require('./setup.js');
const sinon = require('sinon');

const $ = jQuery;
const pf = pageforms;
const mw = mediaWiki;

QUnit.module('pf.originalValueLookup', {
	afterEach: () => {
		sinon.restore();
	}
});

QUnit.test("looks up original values for radio buttons where possible", assert => {
	const radioButtons = $(`
        <div class="radioButtonSpan">
            <label class="radioButtonItem"><input data-original-value="original1" value="value1"></label>
            <label class="radioButtonItem"><input data-original-value="original2" value="value2"></label>
        </div>
    `);
	const element = radioButtons.find('[value="value2"]');

	const lookup = pf.originalValueLookup(element);

	assert.equal('original1', lookup('value1'));
	assert.equal('original2', lookup('value2'));
	assert.equal('value3', lookup('value3'));
});

QUnit.test("looks up original values for autocomplete elements where possible", assert => {
	const element = $(`
        <input autocompletesettings="Site" value="value1">
    `);
	const getStub = sinon.stub();
	getStub.withArgs('wgPageFormsAutocompleteValues').returns({
		Site: {
			"original1": "value1",
			"original2": "value2",
		}
	});
	mw.config = {get: getStub};

	const lookup = pf.originalValueLookup(element);

	assert.equal('original1', lookup('value1'));
	assert.equal('original2', lookup('value2'));
	assert.equal('value3', lookup('value3'));
});

QUnit.test("returns values if no original values are configured", assert => {
	const element = $(`
        <input value="value1">
    `);

	const lookup = pf.originalValueLookup(element);

	assert.equal('value1', lookup('value1'));
	assert.equal('value2', lookup('value2'));
});
