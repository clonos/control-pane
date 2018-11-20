console.log('testing reserve buffer');

var assert = require('assert');
var helper = require('./helper');
var util = require('util');

var commandBegin = "RESERVED 1 4\r\n";
var data1 = new Buffer([1,2,3,4]);
var data2 = new Buffer(data1);
var commandEnd = "\r\n";

var command = new Buffer(commandBegin.length + data1.length + commandEnd.length);
command.write(commandBegin, 0, commandBegin.length, 'binary');
data1.copy(command, commandBegin.length, 0, data1.length);
command.write(commandEnd, commandBegin.length + data1.length, commandEnd.length, 'binary');

helper.bind(function(conn, data) {
	if(String(data) == 'reserve\r\n') {
		conn.write(command);
	}
}, true);

var client = helper.getClient(true);
client.reserve().onSuccess(function(data) {
	var dataBuff = data.data;

	assert(dataBuff instanceof Buffer, "the job data is not a buffer");
	assert(data2.equals(dataBuff), util.inspect(dataBuff) + " !== " + util.inspect(data2));

	console.log('test passed');
	client.disconnect();
});