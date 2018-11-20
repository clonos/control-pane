console.log('testing stats_job not existing');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "stats-job 111111111\r\n") {
		conn.write('NOT_FOUND\r\n');
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.stats_job(111111111).onSuccess(function(data) {
	assert.ok(data);
	assert.ok(data.length);
	assert.equal(data[0], 'NOT_FOUND');
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
