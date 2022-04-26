'use strict';
require('./setup.js');
require('../../res/sfs.js');

const getSfs = (cfg = {}) =>
	window.semanticformsselect(cfg.$, cfg.mw, cfg.pf).private;

QUnit.module('sfs.js', {
	afterEach: () => {
		sinon.restore();
	}
});

QUnit.test("parseName works as expected", assert => {
	const sfs = getSfs();

	assert.deepEqual(sfs.parseFieldIdentifier("TEMPLATE[INDEX][PROPERTY]"),
		{template: "TEMPLATE", index: "INDEX", isList: false, property: "PROPERTY"});

	assert.deepEqual(sfs.parseFieldIdentifier("TEMPLATE[PROPERTY][]"),
		{template: "TEMPLATE", index: null, isList: true, property: "PROPERTY"});

	assert.deepEqual(sfs.parseFieldIdentifier("TEMPLATE[INDEX][PROPERTY][]"),
		{template: "TEMPLATE", index: "INDEX", isList: true, property: "PROPERTY"});
});

QUnit.test("originalValueLookup uses pf.originalValueLookup if available", assert => {
	const pf = {originalValueLookup: sinon.stub().returns(value => 2)};
	const sfs = getSfs({pf: pf});

	const result = sfs.originalValueLookup()(1);

	assert.ok(pf.originalValueLookup.calledOnce);
	assert.equal(result, 2);
});

QUnit.test("originalValueLookup returns identity if pf.originalValueLookup missing", assert => {
	const sfs = getSfs({pf: {}});

	const result = sfs.originalValueLookup()(1);

	assert.equal(result, 1);
});

QUnit.test("parsePlainlistQueryResult parses in the property ('mainlabel=-') case", assert => {
	const sfs = getSfs();

	const result = sfs.parsePlainlistQueryResult([ "X", "Y" ]);

	assert.deepEqual(result, [ [ "X", "X" ], [ "Y", "Y" ] ]);
});
