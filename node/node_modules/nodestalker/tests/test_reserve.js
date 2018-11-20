console.log('testing watch, reserve, use, put');

var port = 11333;

var disconnected = 0;

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == 'use reservetest\r\n') {
		conn.write("USING\r\n");
	}

	if(String(data).indexOf('put') > -1) {
		conn.write("INSERTED 9\r\n");
	}
	
	if(String(data) == 'watch reservetest\r\n') {
		conn.write("WATCHING\r\n");
	}
	
	if(String(data) == 'reserve\r\n') {
		conn.write("RESERVED 9 4\r\ntest\r\n");
	}

	if(String(data) == "delete 9\r\n") {
		conn.write("DELETED\r\n");
	}
}, function() {
	disconnected = disconnected + 1;
	
	if(disconnected == 2) {
		this.close();
	}
});

var client = helper.getClient();
var client2 = helper.getClient();

var success = false;
var error = false;
var success2 = false;
var error2 = false;

var id;
var tube = 'reservetest';

client.watch(tube).onSuccess(function(data) {
	client.reserve().onSuccess(function(data) {
		console.log(data);
		assert.ok(data.id);
		assert.equal(data.data, 'test');
		success = true;

		client.deleteJob(data.id).onSuccess(function(data) {
			client.disconnect();
		});
	});
});

setTimeout(function() {
	client2.use(tube).onSuccess(function(data) {
		client2.put('test').onSuccess(function(data) {
			console.log(data);
			assert.ok(data);
			success2 = true;
			client2.disconnect();
		});
	});
}, 1000);

client.addListener('error', function() {
	error = true;
});

client2.addListener('error', function() {
	error2 = true;
});

process.addListener('exit', function() {
	assert.ok(!error);
	assert.ok(success);
	assert.ok(!error2);
	assert.ok(success2);
	console.log('test passed');
});
