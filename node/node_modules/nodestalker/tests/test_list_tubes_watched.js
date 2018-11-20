console.log('testing list_tubes_watched');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data) == "watch default\r\n") {
		conn.write("WATCHING\r\n");
	}

	if(String(data) == "list-tubes-watched\r\n") {
		var response = 'OK';
		response += "\r\n";
		response += "---\n- default\n  - second\n"
		response += "\r\n";
		conn.write(response);
	}
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.watch('default').onSuccess(function(data) {
	console.log('watch', data);

	client.list_tubes_watched().onSuccess(function(data) {
		assert.ok(data);
		success = true;
		client.disconnect();
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
