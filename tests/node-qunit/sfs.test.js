'use strict';
require('./setup.js');
require('../../res/sfs.js');

const sfs = window.semanticformsselect;

QUnit.module('sfs.js', {
	afterEach: () => {
		sinon.restore();
	}
});

QUnit.test("parseFieldIdentifier works as expected", assert => {
	assert.deepEqual(sfs._parseFieldIdentifier("TEMPLATE[INDEX][PROPERTY]"),
		{template: "TEMPLATE", index: "INDEX", isList: false, property: "PROPERTY"});

	assert.deepEqual(sfs._parseFieldIdentifier("TEMPLATE[PROPERTY][]"),
		{template: "TEMPLATE", index: null, isList: true, property: "PROPERTY"});

	assert.deepEqual(sfs._parseFieldIdentifier("TEMPLATE[INDEX][PROPERTY][]"),
		{template: "TEMPLATE", index: "INDEX", isList: true, property: "PROPERTY"});
});

QUnit.test("parsePlainlistQueryResult parses in title -> display-title", assert => {
	const result = sfs._parsePlainlistQueryResult([ "X (Y)", "Y (A) (Z)" ]);
	assert.deepEqual(result, [ [ "X", "Y" ], [ "Y (A)", "Z" ] ]);
});

QUnit.test("parsePlainlistQueryResult parses in the property ('mainlabel=-') case", assert => {
	const result = sfs._parsePlainlistQueryResult([ "X", "Y" ]);
	assert.deepEqual(result, [ [ "X", "X" ], [ "Y", "Y" ] ]);
});
