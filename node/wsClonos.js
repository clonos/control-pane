// IDE Майкрософта с поддержкой кучи языков и node.js в том числе
// https://github.com/microsoft/vscode

const vars=require('./passwords.js');
//global.vars=vars;

const WebSocket = require('ws');
const fs = require('fs');
const bsWatcher = require('./wsClonos.bsWatcher.js');

const wss = new WebSocket.Server({
	  perMessageDeflate:false,
	  port:8024
});

const clients=[];
const graphs_clients=[];
const graphs_clients_bhyve={};
const graphs_clients_jail={};
const graphs_clients_system={};
const graphs_history={};

const tubes=['racct-bhyve','racct-jail','racct-system'];
for(n in tubes)
{
	graphs_history[tubes[n]]=[];
}

const main_graphs_keys=['name','time','pcpu','pmem'];

global.wss=wss;
wss.on('connection', function connection(ws, req) {
	global.ws=ws;

	ws.on('message', function incoming(message) {
		console.log('WSserver received: %s', message);
	});

	ws.on('close',function wclose(){
		//console.log('ClonOS-WS close server connection!');
		var client=getClient(ws);
		if(client!==false)
		{
			var name=client.name;
			console.log('delete client: '+name);
			delete clients[name];
			if(typeof graphs_clients_bhyve[name]!='undefined') delete graphs_clients_bhyve[name];
			if(typeof graphs_clients_jail[name]!='undefined') delete graphs_clients_jail[name];
			if(typeof graphs_clients_system[name]!='undefined') delete graphs_clients_system[name];
			
			//console.log('clients count: '+getClientsCount());
		}
	});

	ws.on('error',function werror(){
		console.log('ClonOS-WS С server connection error!');
	});

	//ws.send(JSON.stringify(req));
	var url = req.url;
	//console.log(url);
	var pres=url.split('/');
	console.log(url);
	
	//var path=url.substring(0,7);
	//if(path=='/graph/')
	if(pres[1]=='graph')
	{
		graphs_clients.push(pres[2]);
		var tube={'vms':'racct-bhyve','containers':'racct-jail','overview':'racct-system'}[pres[3]];
		var client=pres[4];
		clients[client]={};
		clients[client].name=client;
		clients[client].ws=ws;
		clients[client].first_start=true;
		clients[client].tube=tube;
		switch(tube)
		{
			case 'racct-bhyve':
			graphs_clients_bhyve[client]=ws;
			break;
			case 'racct-jail':
			graphs_clients_jail[client]=ws;
			break;
			case 'racct-system':
			graphs_clients_system[client]=ws;
			break;
		}
		console.log('add client: '+client);
		var a={};
		a.__all=graphs_history[tube];
		ws.send(JSON.stringify(a));
	}
	
});

getMetrics();

function getClient(ws)
{
	for(n in clients)
	{
		if(clients[n].ws==ws) return clients[n];
	}
	return false;
}

function sendJailsMetrics(data,tube)
{
	var tube=data.tube;
	var data=data.data;
	graphs_history[tube].unshift(data);
	graphs_history[tube]=graphs_history[tube].splice(0,25);
	broadcast_graphs_jail(tube);
}

function sendBhyvesMetrics(data,tube)
{
	var tube=data.tube;
	var data=data.data;
	graphs_history[tube].unshift(data);
	graphs_history[tube]=graphs_history[tube].splice(0,25);
	broadcast_graphs_bhyve(tube);
}

function sendSummaryMetrics(data,tube)
{
	var tube=data.tube;
	var data=data.data;
	graphs_history[tube].unshift(data);
	graphs_history[tube]=graphs_history[tube].splice(0,90);
	broadcast_graphs_system(tube);
}


function getMetrics()
{
	new bsWatcher(vars.bs_ip,vars.bs_port,'racct-jail',sendJailsMetrics);
	new bsWatcher(vars.bs_ip,vars.bs_port,'racct-bhyve',sendBhyvesMetrics);
	new bsWatcher(vars.bs_ip,vars.bs_port,'racct-system',sendSummaryMetrics);
}

function broadcast_graphs_bhyve(tube)
{
	var data=graphs_history[tube][0];
	
	for(c in graphs_clients_bhyve)
	{
		var ws=graphs_clients_bhyve[c];
		ws.send(JSON.stringify(data));
	}
}
function broadcast_graphs_jail(tube)
{
	var data=graphs_history[tube][0];
	
	for(c in graphs_clients_jail)
	{
		var ws=graphs_clients_jail[c];
		ws.send(JSON.stringify(data));
	}
	/*
	for(c in clients)
	{
		var cobj=clients[c];
		var ctube=cobj.tube;
		if(graphs_history[tube].length && tube==ctube)
		{
			var data=graphs_history[tube][0];
			
			for(n in data)
			{
			  for(k in data[n])
			  {
				if(main_graphs_keys.indexOf(k)==-1)
					delete data[n][k];
			  }
			}

			cobj.ws.send(JSON.stringify(data));
		}
	}
	*/
}
function broadcast_graphs_system(tube)
{
	var data=graphs_history[tube][0];
	
	for(c in graphs_clients_system)
	{
		var ws=graphs_clients_system[c];
		ws.send(JSON.stringify(data));
	}
}


function getClientsCount()
{
	var count=0;
	for(n in clients)
	{
		count++;
	}
	return count;
}

function tick(path)
{
	/*
	var tube=clients[path].tube;
	//console.log(tube);
	try{
		bs_client.watch(tube).onSuccess(function(data){
			bs_client.reserve().onSuccess(function(job){
				try{
					var arr=JSON.parse(job.data);
					bs_client.deleteJob(job.id);
					var buf=JSON.stringify(arr);
					
					if(clients[path]!=null)
					{
						var client=clients[path]['ws'];
						if(client.readyState===WebSocket.OPEN)
						{
							client.send(buf);
						}
					}
				}catch(e){console.log(e.message);}
			});
		});
	}catch(e){
		console.log(e.message);
	}
	*/
	
	/*
	return;
	var file='/tmp/cbsd_stats.json';
	if(fs.existsSync(file))
	{
		var buf=fs.readFileSync(file,'utf8');
		
		if(clients[path]['first_start'])
		{
			const sqlite3 = require('sqlite3').verbose();
			
			var arr=JSON.parse(buf);
			var n,nl;
			for(n=0,nl=arr.length;n<nl;n++)
			{
				var unit=arr[n];
				this.path=path;
				this.name=unit.name;
				
				var db_name=vars.cbsd_workdir+'/jails-system/'+name+'/racct.sqlite';
				let db = new sqlite3.Database(db_name);
				//let sql = "SELECT '"+this.name+"' as name,idx,memoryuse,pcpu FROM ( SELECT idx, memoryuse, pcpu FROM racct ORDER BY idx DESC LIMIT 50 ) ORDER BY idx ASC;";
				let sql = "SELECT '"+this.name+"' as name,idx,memoryuse,pcpu,pmem FROM racct where idx%5=0 ORDER BY idx DESC LIMIT 25;";
				
				db.all(sql, [], (err, rows) => {
					if (err) {
						console.log(err.message);
						return;
					}
					
					var a={};
					a.__all={};
					
					for(rn in rows)
					{
						var r=rows[rn];
						var name=r.name;
						delete r.name;
						if(typeof a.__all[name]=='undefined') a.__all[name]=[];
						a.__all[name].push(r);
					}
					
					clients[this.path]['ws'].send(JSON.stringify(a));
					clients[this.path]['first_start']=false;
				});
				db.close();
			}
			return;
		}
		
		if(clients[path]!=null)
		{
			var client=clients[path]['ws'];
			if(client.readyState===WebSocket.OPEN)
			{
				client.send(buf);
			}
		}
		clients[path]['first_start']=false;
	}
	*/
	/*
	if(clients[path]!=null)
	{
		//clients[path].send(JSON.stringify(wss.clients));
		var client=clients[path]['ws'];
		if(client.readyState===WebSocket.OPEN)
		{
			client.send(Math.floor(Math.random() * 100) + 1);
		}
	}
	*/
}

/*
var redis = require("redis")
  ,subscriber = redis.createClient({'host':'127.0.0.1'});
//  ,publisher  = redis.createClient();

subscriber.on("message", function(channel, message) {
	console.log("Message '" + message + "' on channel '" + channel + "' arrived!")
	broadcast(wss,message,channel);
});

global.subscriber=subscriber;
global.wss=wss;
wss.on('connection', function connection(ws) {
	global.ws=ws;

	ws.on('message', function incoming(message) {
		console.log('WSserver received: %s', message);
		broadcast(wss,message,path);
	});

	ws.on('close',function wclose(){
		console.log('xdoc close server connection!');
	});

	ws.on('error',function werror(){
		console.log('xdoc — server connection error!'); 
	});

	var path = ws.upgradeReq.url;
  
	subscriber.subscribe(path);
});

function broadcast(server,msg,path) {
    server.clients.forEach(function (conn){
		if(conn.upgradeReq.url==path)
			conn.send(msg);
    })
}

*/