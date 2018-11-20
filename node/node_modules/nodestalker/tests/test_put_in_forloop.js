console.log('testing put in forloop');

var assert = require('assert');
var helper = require('./helper');

var cnt = 0;

helper.bind(function(conn, data) {
	if(String(data).indexOf('put') > -1) {
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
	var completed = 0;

	for (var i=0;i<5;i++) {

	client.put("foo", 100, 0).onSuccess(function(data) {
		completed += 1;
		assert.equal(completed, data);
		

		if(completed === 5) {
			client.disconnect();
		}
	});
	} 
});

