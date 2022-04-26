(function () {
	'use strict';
	const getSfs = (cfg = {}) => window.semanticformsselect(cfg.$, cfg.mw, cfg.pf).private;

	QUnit.module('ext.sfs.unit', QUnit.newMwEnvironment({
		beforeEach: function () {
		},
		afterEach: function () {
		}
	}));

	QUnit.test("parseName works as expected", async assert => {
		const sfs = getSfs();

		assert.deepEqual(sfs.parseFieldIdentifier("TEMPLATE[INDEX][PROPERTY]"),
			{template: "TEMPLATE", index: "INDEX", isList: false, property: "PROPERTY"});

		assert.deepEqual(sfs.parseFieldIdentifier("TEMPLATE[PROPERTY][]"),
			{template: "TEMPLATE", index: null, isList: true, property: "PROPERTY"});

		assert.deepEqual(sfs.parseFieldIdentifier("TEMPLATE[INDEX][PROPERTY][]"),
			{template: "TEMPLATE", index: "INDEX", isList: true, property: "PROPERTY"});
	});

	QUnit.test("originalValueLookup uses pf.originalValueLookup if available", async assert => {
		const pf = {originalValueLookup: sinon.stub().returns(value => 2)};
		const sfs = getSfs({pf: pf});

		const result = sfs.originalValueLookup()(1);

		assert.ok(pf.originalValueLookup.calledOnce);
		assert.equal(result, 2);
	});

	QUnit.test("originalValueLookup returns identity if pf.originalValueLookup missing", async assert => {
		const sfs = getSfs({pf: {}});

		const result = sfs.originalValueLookup()(1);

		assert.equal(result, 1);
	});

	QUnit.test("parsePlainlistQueryResult parses in the property ('mainlabel=-') case", async assert => {
		const sfs = getSfs();

		const result = sfs.parsePlainlistQueryResult(["X", "Y"]);

		assert.deepEqual(result, [["X", "X"], ["Y", "Y"]]);
	});

})();
