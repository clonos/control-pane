console.log('testing put buffer');

var assert = require('assert');
var helper = require('./helper');
var util = require('util');

var data1 = new Buffer([1,2,3,4]);
var data2 = new Buffer([1,2,3,4]);

helper.bind(function(conn, data) {
	if(String(data).indexOf('put') > -1) {
		var dataBuff = extractDataReserveOutput(data);

		assert(data2.equals(dataBuff), util.inspect(dataBuff) + " !== " + util.inspect(data2));

		conn.write("INSERTED 1\r\n");
	}
	
	if(String(data) == "use default\r\n") {
		conn.write('USING default\r\n');
		this.close();
	}
}, false);

// Isn't necessary a raw client to use put with buffers
var client = helper.getClient();
client.use('default').onSuccess(function(data) {
	client.put(data1, 100, 0).onSuccess(function(data) {
		client.disconnect();
	});
});

// Copied from BeanstalkCommand.prototype._extractDataReserveOutput
function extractDataReserveOutput(buff) {
	var i;
	var length = buff.length;

	for(i = 0; i < length - 1; i++) {
		if(buff[i] === 0x0d && buff[i+1] === 0x0a) {
			return buff.slice(i + 2, length - 2);
		}
	}
	return buff;
}