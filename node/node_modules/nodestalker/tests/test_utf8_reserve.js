console.log('testing reserving jobs with utf8 data');

var assert = require('assert');
var helper = require('./helper');

// Contains a 2-byte (ı) and 3-byte (n̈) UTF character
// for a total of 13 bytes in 10 characters
var utfstr = "Spın̈al Tap";

helper.bind(function(conn, data) {
	if(String(data) == "reserve\r\n") {
		conn.write(
			"RESERVED 1234 " + Buffer.byteLength(utfstr, 'utf8') + "\r\n" +
			utfstr + "\r\n"
		);
	}
}, true);
var client = helper.getClient();

var success = false;
var error = false;

client.reserve().onSuccess(function(job) {
	assert.equal(job.data,utfstr)
	success = true;
	client.disconnect();
}).onError(function(job) {
	success = false;
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

