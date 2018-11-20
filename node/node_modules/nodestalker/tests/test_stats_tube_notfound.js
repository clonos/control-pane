console.log('testing stats_tube');

var assert = require('assert');
var helper = require('./helper');

helper.bind(function(conn, data) {
  if(String(data) == 'stats-tube foo\r\n') {
    var response = "";
    response += "NOT_FOUND\r\n";
    conn.write(response);
  }
}, true);
var client = helper.getClient();


var success = false;
var error = false;

client.stats_tube('foo').onSuccess(function(data) {
  console.log(data);
  assert.ok(data);
  success = true;
  client.disconnect();
});

client.addListener('error', function() {
  error = true;
});

process.addListener('exit', function() {
  assert.ok(!error);
  assert.ok(success);
  console.log('test passed');
});
