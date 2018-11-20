var events = require('events'),
	util = require('util');


var connections = {};

chrome.sockets.tcp.onReceive.addListener(function(info) {
	var con = connections[info.socketId];

	if(!con) {
		return;
	}

	con.emit('data', ab2str(info.data));
});

chrome.sockets.tcp.onReceiveError.addListener(function(info) {
	var con = connections[info.socketId];

	if(!con) {
		return;
	}

	con.emit('error', ab2str(info.data));
});

function ab2str(buf) {
	return String.fromCharCode.apply(null, new Uint8Array(buf));
}

function str2ab(str) {
	var buf = new ArrayBuffer(str.length); // 2 bytes for each char
	var bufView = new Uint8Array(buf);
	for (var i=0, strLen=str.length; i < strLen; i++) {
		bufView[i] = str.charCodeAt(i);
	}
	return buf;
}

function Connection(p, a) {

	var self = this;
	this.port = p;
	this.address = a; 
	this.socketId = null;
	this.noDelay = false;
	this.keepAlive = false;

	window.setTimeout(function () {
		chrome.sockets.tcp.create({}, function (info) {
			self.socketId = info.socketId;
			connections[info.socketId] = self;


			chrome.sockets.tcp.connect(self.socketId, self.address, self.port, function (cInfo) {

				if(cInfo < 0) {
					self.emit('error', 'socket connection failed');
					return;
				}

				chrome.sockets.tcp.setNoDelay(self.socketId, self.noDelay, function () {
					chrome.sockets.tcp.setKeepAlive(self.socketId, self.keepAlive, 100, function () {
						self.emit('connect');
					});
				});
			});
		});
	}, 10);
}

util.inherits(Connection, events.EventEmitter);

Connection.prototype.write = function (data) {
	var self = this;

	chrome.sockets.tcp.send(this.socketId, str2ab(data), function (info) {
		//self.emit('close');
		if(info.resultCode < 0) {
			self.emit('error', info.resultCode);
		}
	});
};

Connection.prototype.close = function () {
	var self = this;

	chrome.sockets.tcp.close(this.socketId, function () {
		self.emit('close');
	});
};

Connection.prototype.end = function () {
	var self = this;

	chrome.sockets.tcp.disconnect(this.socketId, function () {
		self.emit('disconnect');
		delete connections[self.socketId];
	});
};

Connection.prototype.destroy = function () {
	// noop
};

Connection.prototype.setNoDelay = function () {
	this.noDelay = true;
};

Connection.prototype.setKeepAlive = function () {
	this.keepAlive = true;
};

module.exports = {
	createConnection: function (p, a) {
		return new Connection (p, a);
	}
}