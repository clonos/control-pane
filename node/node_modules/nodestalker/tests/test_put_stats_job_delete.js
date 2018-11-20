console.log('testing put stats_job');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data).indexOf('put') > -1) {
		conn.write("INSERTED 3\r\n");
	}

	if(String(data) == 'stats-job 3\r\n') {
		response = "";
		response += "OK 141\r\n";
		response += "---\n";
		response += "id: 3\n";
		response += "tube: default\n";
		response += "state: ready\n";
		response += "pri: 10\n";
		response += "age: 0\n";
		response += "delay: 0\n";
		response += "ttr: 100000\n";
		response += "time-left: 0\n";
		response += "reserves: 0\n";
		response += "timeouts: 0\n";
		response += "releases: 0\n";
		response += "buries: 0\n";
		response += "kicks: 0\n";
		response += "\r\n";
		conn.write(response);
	}

	if(String(data) == 'delete 3\r\n') {
		conn.write("DELETED\r\n");
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.put('test').onSuccess(function(job_data) {
	var test_id = job_data[0];

	client.stats_job(test_id).onSuccess(function(data) {
		assert.ok(data);
		assert.ok(data.id);
		assert.equal(test_id, data.id);
		success = true;

		client.deleteJob(test_id).onSuccess(function() {
			client.disconnect();
		});
	});
});

client.addListener('error', function() {
	error = true;
});

process.addListener('exit', function() {
	assert.ok(!error);
	assert.ok(success);
	console.log('test passed');
});
