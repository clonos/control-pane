define(
	"main",
	[
		"MessageList"
	],
	function(MessageList) {
		var ws = new WebSocket("ws://samson.bsdstore.ru:8082/entry");
		var list = new MessageList(ws);
		ko.applyBindings(list);
	}
);
