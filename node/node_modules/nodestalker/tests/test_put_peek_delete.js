console.log('testing put, peek, delete');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data).indexOf('put') > -1) {
		conn.write("INSERTED 10\r\n");
	}

	if(String(data) == 'peek 10\r\n') {
		conn.write("FOUND 10 7\r\ntest\r\n");
	}

	if(String(data) == 'delete 10\r\n') {
		conn.write("DELETED\r\n");
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.put('test').onSuccess(function(data) {
	console.log(data);
	var test_id = data[0];
	
	client.peek(test_id).onSuccess(function(data) {
		console.log(data);
		assert.ok(data);
		assert.equal(data.id, test_id);
		assert.equal(data.data, 'test');
		assert.equal(typeof data, 'object');
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