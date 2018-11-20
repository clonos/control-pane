console.log('testing stats_tube');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'stats-tube default\r\n') {
		var response = "";
		response += "OK 251\r\n";
		response += "---\n";
		response += "name: default\n";
		response += "current-jobs-urgent: 0\n";
		response += "current-jobs-ready: 0\n";
		response += "current-jobs-reserved: 0\n";
		response += "current-jobs-delayed: 0\n";
		response += "current-jobs-buried: 0\n";
		response += "total-jobs: 8\n";
		response += "current-using: 1\n";
		response += "current-watching: 1\n";
		response += "current-waiting: 0\n";
		response += "cmd-pause-tube: 0\n";
		response += "pause: 0\n";
		response += "pause-time-left: 0\n";
		response += "\r\n";
		conn.write(response);
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.stats_tube('default').onSuccess(function(data) {
	console.log(data);
	assert.ok(data);
	assert.ok(data.name);
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
