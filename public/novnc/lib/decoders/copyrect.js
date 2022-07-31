"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

/*
 * noVNC: HTML5 VNC client
 * Copyright (C) 2019 The noVNC Authors
 * Licensed under MPL 2.0 (see LICENSE.txt)
 *
 * See README.md for usage and integration instructions.
 *
 */
var CopyRectDecoder = /*#__PURE__*/function () {
  function CopyRectDecoder() {
    _classCallCheck(this, CopyRectDecoder);
  }

  _createClass(CopyRectDecoder, [{
    key: "decodeRect",
    value: function decodeRect(x, y, width, height, sock, display, depth) {
      if (sock.rQwait("COPYRECT", 4)) {
        return false;
      }

      var deltaX = sock.rQshift16();
      var deltaY = sock.rQshift16();

      if (width === 0 || height === 0) {
        return true;
      }

      display.copyImage(deltaX, deltaY, x, y, width, height);
      return true;
    }
  }]);

  return CopyRectDecoder;
}();

exports["default"] = CopyRectDecoder;