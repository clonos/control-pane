console.log('testing use, put, watch, reserve, bury, peek_buried');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'use burytest\r\n') {
		conn.write("USING\r\n");
	}

	if(String(data).indexOf('put') > -1) {
		conn.write("INSERTED 9\r\n");
	}
	
	if(String(data) == 'watch burytest\r\n') {
		conn.write("WATCHING\r\n");
	}
	
	if(String(data) == 'reserve\r\n') {
		conn.write("RESERVED 9 8\r\nburytest\r\n");
	}

	if(String(data) == 'bury 9 10\r\n') {
		conn.write("BURIED\r\n");
	}

	if(String(data) == 'peek-buried\r\n') {
		conn.write("FOUND 9 8\r\nburytest\r\n");
	}

	if(String(data) == "delete 9\r\n") {
		conn.write("DELETED\r\n");
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.use('burytest').onSuccess(function(data) {
	client.put('burytest').onSuccess(function(data) {
		var test_id = data[0];
	
		client.watch('burytest').onSuccess(function() {
			client.reserve().onSuccess(function(data) {
				client.bury(test_id).onSuccess(function(data) {
					client.peek_buried().onSuccess(function(data) {
						assert.ok(data);
						assert.equal(data.id, test_id);
						assert.equal(data.data, 'burytest');
						assert.equal(typeof data, 'object');
						success = true;
		
						client.deleteJob(test_id).onSuccess(function() {
							client.disconnect();
						});
					});
				});
			});
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