#!/usr/bin/env node
/* eslint-disable no-var */
'use strict'

var fs = require('fs')
var shasum = require('.')

var input = process.argv[2]
if (input && input !== '-') {
  console.log(shasum(fs.readFileSync(input)))
} else {
  var buffers = []
  process.stdin.on('data', function (chunk) { buffers.push(chunk) })
  process.stdin.on('end', function () {
    console.log(shasum(Buffer.concat(buffers)))
  })
}
