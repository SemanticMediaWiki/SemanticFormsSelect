if (!global.tests_initialized) {
	const { TextEncoder, TextDecoder } = require('util');
	global.TextEncoder = TextEncoder;
	global.TextDecoder = TextDecoder;

	const jsdom = require('jsdom');
	global.window = new jsdom.JSDOM().window;
	global.jQuery = require('../../../../resources/lib/jquery/jquery.js');

	global.mediaWiki = {};
	global.pageforms = {};
	require('../../res/pf.originalValueLookup.js');
	require('../../res/sfs.js');

	global.sinon = require('sinon');

	global.tests_initialized = true;
}
