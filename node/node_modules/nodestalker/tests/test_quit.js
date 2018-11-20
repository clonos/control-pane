console.log('testing quit');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "use default\r\n") {
		conn.write('USING default\r\n');
	}

	if(String(data) == "quit\r\n") {
		conn.destroy();
		this.close();
	}
}, false);
var client = helper.getClient();


var success = false;
var closed = false;

client.use('default').onSuccess(function(data) {
	assert.ok(data);
	client.quit();
});

client.addListener('close', function() {
	closed = true;
});

process.addListener('exit', function() {
	assert.ok(closed);
	console.log('test passed');
});

