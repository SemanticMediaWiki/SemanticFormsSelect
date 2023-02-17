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
	const parse = sfs._parsePlainlistQueryResult;

	assert.deepEqual(parse([ "X (Y)" ]), [ [ "X", "Y" ] ]);
	assert.deepEqual(parse([ "X (Y) 1" ]), [ [ "X", "Y" ] ]);
	assert.deepEqual(parse([ " X  ( Y  ) " ]), [ [ "X", "Y" ] ]);
	assert.deepEqual(parse([ "Y (A) (Z)" ]), [ [ "Y (A)", "Z" ] ]);
	assert.deepEqual(parse([ "X (Y(5)) (Z(1))" ]), [ [ "X (Y(5))", "Z(1)" ] ]);
	assert.deepEqual(parse([ "X (1 (Y)" ]), [ [ "X (1", "Y" ] ]);
});

QUnit.test("parsePlainlistQueryResult parses in the property ('mainlabel=-') case", assert => {
	const result = sfs._parsePlainlistQueryResult([ "X", "Y" ]);
	assert.deepEqual(result, [ [ "X", "X" ], [ "Y", "Y" ] ]);
});
