console.log('testing peek_delayed');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'peek-delayed\r\n') {
		conn.write("NOT_FOUND\r\n");
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.peek_delayed().onSuccess(function(data) {
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
