console.log('testing utf8');

var cnt = 0;

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
	if(String(data).indexOf('put') > -1) {
		var input = data.toString().split('\r\n');

		assert.equal(input[1], "ééééé");
		assert.equal(Buffer.byteLength(input[1], 'utf8'), Buffer.byteLength("ééééé", 'utf8'));

		cnt += 1;
		conn.write("INSERTED "+cnt+"\r\n");
	}
	
	if(String(data) == "use default\r\n") {
		conn.write('USING default\r\n');
		this.close();
	}
}, false);
var client = helper.getClient();

client.use('default').onSuccess(function(data) {
	client.put("ééééé", 100, 0).onSuccess(function(data) {
		assert.ok(!isNaN(data[0]));
		client.disconnect();
	});
});

