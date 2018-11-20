console.log('testing ignore');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "ignore default\r\n") {
		conn.write('WATCHING');
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.ignore('default').onSuccess(function(data) {
	console.log(data);
		assert.ok(data);
	assert.equal(typeof data, 'object');
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

