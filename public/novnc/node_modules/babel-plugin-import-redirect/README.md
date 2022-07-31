# babel-plugin-import-redirect

[![Build Status](https://travis-ci.org/Velenir/babel-plugin-import-redirect.svg?branch=master)](https://travis-ci.org/Velenir/babel-plugin-import-redirect) [![npm version](https://badge.fury.io/js/babel-plugin-import-redirect.svg)](https://badge.fury.io/js/babel-plugin-import-redirect) [![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/) [![dependencies Status](https://david-dm.org/velenir/babel-plugin-import-redirect/status.svg)](https://david-dm.org/velenir/babel-plugin-import-redirect) [![devDependencies](https://david-dm.org/velenir/babel-plugin-import-redirect/dev-status.svg)](https://david-dm.org/velenir/babel-plugin-import-redirect?type=dev) [![Greenkeeper badge](https://badges.greenkeeper.io/Velenir/babel-plugin-import-redirect.svg)](https://greenkeeper.io/) [![codecov](https://codecov.io/gh/Velenir/babel-plugin-import-redirect/branch/master/graph/badge.svg)](https://codecov.io/gh/Velenir/babel-plugin-import-redirect) [![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://github.com/velenir/babel-plugin-import-redirect/blob/master/LICENSE)

A [Babel](https://babeljs.io/) plugin that allows to point **import**, **export from** declarations, **require()** and simple dynamic **import()** (only string literal as its argument) calls to custom paths. This can be especially useful in testing — for swapping regular production and development files and modules with their mock implementations.

For example, this plugin allows to transform:

```
import "./path/to/file";
export {variable} from "./path/to/different/file";
require("module");
import("different_module");
```

to

```
import "./mocks/mockFile";
export {variable} from "./mocks/differentMockFile";
require("./mocks/mockModule");
import("yet_another_module");
```

# Usage

Install the plugin with

```
npm install --save-dev babel-plugin-import-redirect
```

Then add it to your babel configuration (e.g. in *.babelrc*). A rather exhaustive setup may look like this:

```
{
  "plugins": [
    "syntax-dynamic-import",
    ["import-redirect",
    {
      "root": "./tests/mocks",
      "extraFunctions": ["custom_require_function", "SystemJS.import"],
      "promisifyReplacementFor": "SystemJS.import",
      "redirect": {
        "connect": "./connect.mocked",
        "path/to/(\\w+)\\.js$": "./$1.mocked",
        "\\.css$" : false,
        "path/to/globals": {"MY_GLOBAL_1": true, "MY_GLOBAL_2": 42}
      }
    }]
  ]
}
```

> Transforming dynamic **import()** requires that [babel-plugin-syntax-dynamic-import](https://github.com/babel/babel/tree/master/packages/babel-plugin-syntax-dynamic-import) be included in `plugins` before `import-redirect`

Now when you transpile your source files, any path inside **import**, **export from** declarations, **require()** and dynamic **import()** calls that matches a *redirect.key* is resolved to point to the file from a corresponding *redirect.value*.

## Example

To provide an example, given a project structure of

```
./
  node_modules/
    connect/
  tests/
    mocks/
      connect.mocked.js
      lib.mocked.js
  src/
    index.js
    helpers/
      globals.js
    libs/
      lib.js
    style.css
  .babelrc
```

with *index.js* of

```
import "./style.css";
import connect from "connect";
export {default as libFunction} from "./helpers/lib";
import {MY_GLOBAL_1, MY_GLOBAL_2} from "./helpers/globals";
// ...
```

and *.babelrc* of

```
{
  "plugins": [
    ["import-redirect",
    {
      "root": "./tests/mocks",
      "redirect": {
        "connect": "./connect.mocked",
        "/libs/(\\w+)\\.js$": "./$1.mocked",
        "\\.css$" : false,
        "helpers/globals.js$": {"MY_GLOBAL_1": true, "MY_GLOBAL_2": 42}
      }
    }]
  ]
}
```

The *index.js* will transpile to

```
import connect from "../tests/mocks/connect.mocked";
export {default as libFunction} "../tests/mocks/lib.mocked";
const {MY_GLOBAL_1, MY_GLOBAL_2} = {"MY_GLOBAL_1": true, "MY_GLOBAL_2": 42};
// ...
```

The transpilation to make it happen will be performed as follows:

1. **import "./style.css";**``
    1. `./style.css` path is resolved to `project/src/style.css` absolute path
    1. `project/src/style.css` is matched against `new RegExp("\\.css$")`
    1. the corresponding value of false triggers removal of the import declaration
> removed

2. **import connect from "connect";**
    1. `connect` path is resolved to `project/node_modules/connect/...` absolute path
    2. the path matches against `new RegExp("connect")`
    3. the redirected path of `./test/mocks/connect.mocked` is resolved relative to `index.js` as  `../tests/mocks/connect.mocked`
    4. the path is changed to `../tests/mocks/connect.mocked`
> replaced with `import connect from "../tests/mocks/connect.mocked";`

3. **import libFunction from "./helpers/lib";**
    1. `./helpers/lib` path is resolved to `project/node_modules/helpers/lib.js` absolute path
    2. the path matches against `new RegExp("/libs/(\\w+)\\.js$")`
    3. as the redirect value contains a replacement group (`$1`), it is converted to `./tests/mocks/lib.mocked`
    4. the redirected path of `./tests/mocks/lib.mocked` is resolved relative to `index.js` as  `../tests/mocks/lib.mocked`
    5. the path is changed to `../tests/mocks/lib.mocked`
> replaced with `import libFunction from "../tests/mocks/lib.mocked";`

4. **import {MY_GLOBAL_1, MY_GLOBAL_2} from "./helpers/globals";**
    1. `./helpers/globals` path is resolved to `project/src/helpers/globals.js` absolute path
    2. `project/src/helpers/globals.js` matches against `new RegExp("helpers/globals.js$")`
    3. the corresponding value of an object triggers replacement of the import declaration with a variable declaration
> replaced with `const {MY_GLOBAL_1, MY_GLOBAL_2} = {"MY_GLOBAL_1": true, "MY_GLOBAL_2": 42};`

## Options

```
{
  "root": String,
  "extraFunctions": String | Array<String>,
  "promisifyReplacementFor": String | Array<String>,
  "redirect": {
    matchPattern: replacement
  },
  "extensions": Array<String>,
  "suppressResolveWarning": Boolean
}
```

+ `root` : path, relative to which `replacement` paths are resolved. Equals project root folder by default.
+ `extraFunctions` : functions to consider when matching against keys in redirect in addition to **import**, **export from** declarations, **require()** and dynamic **import()**. It can be a simple function name (`"custom_require"`) or an object.property pair (`"SystemJS.import"`).
+ `promisifyReplacementFor` : functions, in addition to `import()`, for which `replacement` Objects should be wrapped in `Promise.resolve()`.
+ `redirect` : Object with `matchPattern` keys and `replacement` values.
+ `extensions`: Array of extensions to use for resolving filenames. Equals `[".js", ".jsx", ".es", "es6"]` by default, providing custom extensions will override the default.
+ `suppressResolveWarning`: Boolean, `false` by default. During path resolution plugin shows a warning when it can't find a module. It will still do its best to resolve to the right path. This option suppresses that warning.

#### matchPattern

A `String` to be used as a pattern in a `RegExp`. This `RegExp` will be matched against the source of **import** and **export from** declarations and the first argument of **require()**, **import()** and functions from `extraFunctions`. If the match is successful the whole expression will be transformed depending on the corresponding `replacement`.

Take care to escape (`\`) every special character, namely backslash (`\`). That is, escape twice every time you would escape once in a literal regexp. E.g. a `RegExp` constructed from `"\\w+"` pattern is equivalent to `/\w+/`, to use backslash in your pattern escape it like so `"\\\\"`.

> To match *only* the node module `required_module` and not accidentally pick up paths that would otherwise match `/required_module/` (e.g. `"./src/my_required_module/index.js"`), it is recommended to specialize the pattern like this: `"/node_modules/required_module/"`.

#### replacement
Can be
+ A `String` path to a file to be used in place of the originally `require`d / `import`ed file. The path will be resolved relative to `root` if provided or to project root folder (`process.cwd()`) otherwise.
If `replacement` contains a replacement group (e.g. `$1`), a corresponding parenthesized match result from the `matchPattern` will be substituted in prior to resolving the path.
E.g. given a project structure of

```
./
  src/
    index.js
    lib.js
  mocks/
    lib.js
```

`require("./lib");` inside `./src/index.js` file when matched against `"/(\\w+).js": "./mocks/$1"` with no `root` provided will transpile to `require("../mocks/lib");`.

+ `false`, which will result in removal of simple `import` declarations, `require()`, `import()` and custom require function calls without side effects. That is, functions which are not part of a larger expression:

```
// will be removed
require("path/to/file");
import("path/to/file");

// won't be removed
const lib = require("path/to/file");
require("path/to/file").prop;
fn(require("path/to/file"));
import("path/to/file").then(module => module.default);
```

and simple, non-named, non-namespace, non-default import statements:

```
// will be removed
import "path/to/file";

// won't be removed
import lib from "pat/to/file";
import * as lib from "pat/to/file";
import {lib} from "pat/to/file";
```

+ An `Object`, which will result in removal of simple `import` declarations, `require()`, `import()` and custom require function calls without side effects (same as for `false`) and in replacement of default, named, namespace `import`s, `require()`, `import()` and custom require function calls with these objects. This `Object` must be JSON-serialazable.
Additionally **replacement** objects for `import()` calls and calls of custom functions from `promisifyReplacementFor` will be wrapped in `Promise.resolve()`.

E.g. for a `"path/to/file": {"key": val}` **matchPattern - replacement** pair

| will be removed                                          | was removed
|:---------------------------------------------------------|:-|
| `import "path/to/file";`                                 |  |
| `require("path/to/file");`                               |  |
| `import("path/to/file");`                                |  |
| **will be replaced**                                     | **was replaced with**
| `const lib = require("path/to/file");`                   | `const lib = {"key": val};`
| `require("path/to/file").prop; `                         | `({"key": val}).prop;`
| `fn(require("path/to/file"));`                           | `fn({"key": val});`
| `import("path/to/file").then(module => module.default);` | `Promise.resolve({"key": val}).then(module => module.default);`
| `import lib from "pat/to/file";`                         | `const {default: lib} = {"key": val};`
| `import * as lib from "pat/to/file";`                    | `const lib = {"key": val};`
| `import {lib} from "pat/to/file";`                       | `const {lib} = {"key": val};`
| `import lib, {lib1 as lib2} from "./style.css";`         | `const {default: lib, lib1: lib2} = {"key": val};`
| `import lib, * as libAll from "./style.css";`            | `const libAll = {"key": val}, {default: lib} = libAll;`

> To summarize,
> + a `String` replacement handles **import**, **export from** declarations, **require()**, simple dynamic **import()** and custom function calls.
> + `false` replacement removes aforementioned declarations and function calls, except for **export from**, without side effects.
> + `Object` replacement does the same as `false` and also replaces relevant expressions with side effects.
