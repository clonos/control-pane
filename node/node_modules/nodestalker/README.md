A Beanstalk client utilising node.js
Tested for beanstalkd 1.4.6

[![Build Status](https://travis-ci.org/pascalopitz/nodestalker.png)](https://travis-ci.org/pascalopitz/nodestalker)

[![Dependencies](https://david-dm.org/pascalopitz/nodestalker.png)](https://david-dm.org/pascalopitz/nodestalker)

[![NPM](http://nodei.co/npm/nodestalker.png)](http://nodei.co/npm/nodestalker/)

## INSTALL

    npm install nodestalker


## USAGE

Simple usage example:

    var bs = require('nodestalker'),
        client = bs.Client('127.0.0.1:11300');

    client.use('default').onSuccess(function(data) {
      console.log(data);

      client.put('my job').onSuccess(function(data) {
    	console.log(data);
    	client.disconnect();
      });
    });




### How do I reserve multiple items?

Each client basically represents one open socket to beanstalkd.
Each command call just pumps one command into that socket, which then expects a corresponding return.

The server should maintain the state of the socket.
However, reserve (or reserve with timeout) will only pull one job.
You should then be able to reserve again on the same socket with the state of watch and ignore still preserved by the server.

Probably the most common usage scenario:

    var bs = require('nodestalker'),
        client = bs.Client('127.0.0.1:11300'),
        tube = 'test_tube';

    client.watch(tube).onSuccess(function(data) {
        function resJob() {
            client.reserve().onSuccess(function(job) {
                console.log('reserved', job);

                client.deleteJob(job.id).onSuccess(function(del_msg) {
                    console.log('deleted', job);
                    console.log('message', del_msg);
                    resJob();
                });
            });
        }

        resJob();
    });

If you want to do this fully in a fully asynchronous way, because there's a blocking process happening otherwise, you'll have to work with multiple sockets.
This means that you'll have to repeat watch and ignore commands for each socket.

    var bs = require('nodestalker'),
        tube = 'test_tube';

    function processJob(job, callback) {
        // doing something really expensive
        console.log('processing...');
        setTimeout(function() {
            callback();
        }, 1000);
    }

    function resJob() {
        var client = bs.Client('127.0.0.1:11300');

        client.watch(tube).onSuccess(function(data) {
            client.reserve().onSuccess(function(job) {
                console.log('received job:', job);
                resJob();

                processJob(job, function() {
                    client.deleteJob(job.id).onSuccess(function(del_msg) {
                        console.log('deleted', job);
                        console.log(del_msg);
                        client.disconnect();
                    });
                    console.log('processed', job);
                });
            });
        });
    }

    resJob();


## DOCUMENTATION

    npm run-script docs

Annotated source is now in the docs folder

## TESTING

Also there are some tests now.
Please make sure beanstalkd is running on the default settings.

To run all tests:

    npm test


## CREDIT

Depends on the yaml package by nodeca.

https://github.com/nodeca/js-yaml

Thanks to people that took time to fix some things.

aahoughton
andho
jney
nmcquay
tokudu
justinwalsh
yeldarby
cincodenada
