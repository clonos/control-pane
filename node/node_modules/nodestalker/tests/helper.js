var bs = require('../lib/beanstalk_client');
var net = require('net');

var port = process.env.BEANSTALK_PORT || 11333;

var mock = process.env.BEANSTALKD !== '1';
var mock_server;

var connection;

module.exports = {
	bind : function (fn, closeOnEnd) {

		if(!mock) {
			return false;
		}

		mock_server = net.createServer(function(conn) {
			connection = conn;

			connection.on('data', function (data) {
				fn.call(mock_server, connection, data);
			});

			if(closeOnEnd === true) {
				closeOnEnd = function () {
					mock_server.close();
				}
			}

			if(closeOnEnd) {
				connection.on('end', function () {
					closeOnEnd.call(mock_server); 
				});
			}
		});

		mock_server.listen(port);
	},
	getClient : function (isRaw) {
		return bs.Client('127.0.0.1:' + port, isRaw || false);
	},
	activateDebug : function() {
		bs.Debug.activate();
	}
}