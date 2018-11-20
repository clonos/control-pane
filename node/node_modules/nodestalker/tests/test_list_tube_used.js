console.log('testing list_tube_used');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "list-tube-used\r\n") {
		var response = 'USING';
		response += "\r\n";
		response += "tube"
		response += "\r\n";
		conn.write(response);
	}
}, true);
var client = helper.getClient();

var success = false;
var error = false;

client.list_tube_used().onSuccess(function(data) {
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