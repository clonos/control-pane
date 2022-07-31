# karma-script-launcher

[![js-standard-style](https://img.shields.io/badge/code%20style-standard-brightgreen.svg?style=flat-square)](https://github.com/feross/standard)
 [![npm version](https://img.shields.io/npm/v/karma-script-launcher.svg?style=flat-square)](https://www.npmjs.com/package/karma-script-launcher) [![npm downloads](https://img.shields.io/npm/dm/karma-script-launcher.svg?style=flat-square)](https://www.npmjs.com/package/karma-script-launcher)

[![Build Status](https://img.shields.io/travis/karma-runner/karma-script-launcher/master.svg?style=flat-square)](https://travis-ci.org/karma-runner/karma-script-launcher) [![Dependency Status](https://img.shields.io/david/karma-runner/karma-script-launcher.svg?style=flat-square)](https://david-dm.org/karma-runner/karma-script-launcher) [![devDependency Status](https://img.shields.io/david/dev/karma-runner/karma-script-launcher.svg?style=flat-square)](https://david-dm.org/karma-runner/karma-script-launcher#info=devDependencies)

> Shell script launcher for [Karma](https://github.com/karma-runner/karma)

This plugin allows you to use a shell script as a browser launcher. The script has to accept
a single argument - the url that the browser should open.

## Installation

Install using

```bash
$ npm install karma-script-launcher --save-dev
```

## Configuration

```js
// karma.conf.js
module.exports = function(config) {
  config.set({
    browsers: ['/usr/local/bin/my-custom.sh']
  })
}
```

You can pass list of browsers as a CLI argument too:

```bash
$ karma start --browsers /some/custom/script.sh
```

----

For more information on Karma see the [homepage].

[homepage]: http://karma-runner.github.com
