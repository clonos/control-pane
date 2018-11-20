console.log('testing reserve with chunked response');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'reserve\r\n') {
		conn.write("RESERVED 9 10\r\ntest\r\n");
	}
	
	setTimeout(function () {
		conn.write("test\r\n");
	}, 100)
}, true);
var client = helper.getClient();

var success = false;
var error = false;

client.reserve().onSuccess(function(data) {
	assert.ok(data);
	success = true;
	client.disconnect();
});

client.addListener('error', function() {
	error = true;
});

process.addListener('exit', function() {
	assert.ok(!error);
	assert.ok(success);
	console.log('test passed');
});
