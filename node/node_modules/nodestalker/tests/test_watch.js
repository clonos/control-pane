console.log('testing watch');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "watch default\r\n") {
		conn.write('WATCHING 1\r\n');
		this.close();
	}
});
var client = helper.getClient();

var success = false;
var error = false;

client.watch('default').onSuccess(function(data) {
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
