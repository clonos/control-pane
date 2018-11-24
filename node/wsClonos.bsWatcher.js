const bs=require('nodestalker');

var bsWatcher=function(ip,port,tube,callback)
{
    this.ip=ip;
    this.port=port;
    this.tube=tube;
    this.callback=callback;
    this.client=null;
    this.connectionError=false;

    this.connect();
}
bsWatcher.prototype.connect=function()
{
    this.client=bs.Client('localhost:11300');
    this.client.addListener('connect', function() {
        this.connectionError=false;
    }.bind(this));
     this.client.addListener('end', function(err) {
        this.connectionError=true;
        setTimeout(this.connect.bind(this),3000);
    }.bind(this));
    this.client.addListener('close', function(err) {
        console.log('connection closed');
    }.bind(this));
    this.watch();
}
bsWatcher.prototype.watch=function()
{
    if(!this.connectionError)
    {
        this.client.watch(this.tube).onSuccess(this.reserve.bind(this));
    }
}
bsWatcher.prototype.reserve=function()
{
    if(!this.connectionError)
    {
        this.client.reserve().onSuccess(this.getJob.bind(this));
    }
}
bsWatcher.prototype.getJob=function(job)
{
    var arr=JSON.parse(job.data);
    if(!this.connectionError)
    {
        this.client.deleteJob(job.id).onSuccess(this.reserve.bind(this));
        this.callback(arr,this.tube);    
    }
}

module.exports=bsWatcher;