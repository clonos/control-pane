console.log('testing reserve_with_timeout');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'reserve-with-timeout 2\r\n') {
		conn.write("RESERVED 9 4\r\ntest\r\n");
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.reserve_with_timeout(2).onSuccess(function(data) {
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
