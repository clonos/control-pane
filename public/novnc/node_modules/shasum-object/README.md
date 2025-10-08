# shasum-object

Get the shasum of a buffer or object.

[Description](#description) - [Install](#install) - [Usage](#usage) - [License: Apache-2.0](#license)

[![npm][npm-image]][npm-url]
[![actions][actions-image]][actions-url]
[![standard][standard-image]][standard-url]

[npm-image]: https://img.shields.io/npm/v/shasum-object.svg?style=flat-square
[npm-url]: https://www.npmjs.com/package/shasum-object
[actions-image]: https://img.shields.io/github/actions/workflow/status/goto-bus-stop/shasum-object/ci.yml
[actions-url]: https://github.com/goto-bus-stop/shasum-object/actions/workflows/ci.yml
[standard-image]: https://img.shields.io/badge/code%20style-standard-brightgreen.svg?style=flat-square
[standard-url]: http://npm.im/standard

## Description

shasum-object computes a hash string for strings, buffers, and JSON objects.

Sha1 is used by default, but other algorithms provided by Node.js are supported.

shasum-object is committed to supporting all Node.js versions 0.8 and up.

This is a spiritual successor to [shasum](https://github.com/dominictarr/shasum).

## Install

```
npm install shasum-object
```

## Usage

```js
var fs = require('fs')
var shasum = require('shasum-object')

shasum('of a string')
shasum(fs.readFileSync('of-a-file.txt'))

shasum({
  of: ['an', 'object']
})
```

## API
### `shasum(input, algorithm = 'sha1', encoding = 'hex')`

Compute the hash for the given input.
- `input` - a string, buffer or JSON object. objects are stringified using [`fast-safe-stringify`](https://github.com/davidmarkclements/fast-safe-stringify).
- `algorithm` - the hash algorithm to use. see [`crypto.createHash`](https://nodejs.org/api/crypto.html#crypto_crypto_createhash_algorithm_options).
- `encoding` - how to encode the hash result. see [`hash.digest`](https://nodejs.org/api/crypto.html#crypto_hash_digest_encoding).

## License

[Apache-2.0](LICENSE.md)
