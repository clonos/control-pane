console.log('testing reserve with chunked response that times out');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'reserve\r\n') {
		conn.write("RESERVED 9 10\r\ntest\r\n");
	}
	
}, true);
var client = helper.getClient();

var success = false;
var error = false;

client.reserve().onError(function(data) {
	success = true;
	client.disconnect();
});


process.addListener('exit', function() {
	assert.ok(success);
	console.log('test passed');
});
