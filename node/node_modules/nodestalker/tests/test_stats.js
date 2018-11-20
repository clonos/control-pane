console.log('testing stats');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'stats\r\n') {
		var response = "";
		response += "OK 813\r\n";
		response += "---\n";
		response += "current-jobs-urgent: 0\n";
		response += "current-jobs-ready: 0\n";
		response += "current-jobs-reserved: 0\n";
		response += "current-jobs-delayed: 0\n";
		response += "current-jobs-buried: 0\n";
		response += "cmd-put: 10\n";
		response += "cmd-peek: 2\n";
		response += "cmd-peek-ready: 0\n";
		response += "cmd-peek-delayed: 0\n";
		response += "cmd-peek-buried: 1\n";
		response += "cmd-reserve: 2\n";
		response += "cmd-reserve-with-timeout: 0\n";
		response += "cmd-delete: 10\n";
		response += "cmd-release: 0\n";
		response += "cmd-use: 2\n";
		response += "cmd-watch: 2\n";
		response += "cmd-ignore: 0\n";
		response += "cmd-bury: 1\n";
		response += "cmd-kick: 0\n";
		response += "cmd-touch: 0\n";
		response += "cmd-stats: 1\n";
		response += "cmd-stats-job: 6\n";
		response += "cmd-stats-tube: 0\n";
		response += "cmd-list-tubes: 15\n";
		response += "cmd-list-tube-used: 0\n";
		response += "cmd-list-tubes-watched: 0\n";
		response += "cmd-pause-tube: 0\n";
		response += "job-timeouts: 0\n";
		response += "total-jobs: 10\n";
		response += "max-job-size: 65535\n";
		response += "current-tubes: 1\n";
		response += "current-connections: 1\n";
		response += "current-producers: 0\n";
		response += "current-workers: 0\n";
		response += "current-waiting: 0\n";
		response += "total-connections: 27\n";
		response += "pid: 2759\n";
		response += "version: 1.4.6\n";
		response += "rusage-utime: 0.000000\n";
		response += "rusage-stime: 0.028001\n";
		response += "uptime: 19109\n";
		response += "binlog-oldest-index: 0\n";
		response += "binlog-current-index: 0\n";
		response += "binlog-max-size: 10485760\n";
		response += "\r\n";
		conn.write(response);
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.stats().onSuccess(function(data) {
	console.log(data);
	assert.ok(data);
	assert.ok(data.pid);
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
