console.log('testing use');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "use default\r\n") {
		conn.write('USING default\r\n');
		this.close();
	}
}, false);
var client = helper.getClient();


var success = false;
var error = false;

client.use('default').onSuccess(function(data) {
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

