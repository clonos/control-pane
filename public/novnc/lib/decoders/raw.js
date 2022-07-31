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
var RawDecoder = /*#__PURE__*/function () {
  function RawDecoder() {
    _classCallCheck(this, RawDecoder);

    this._lines = 0;
  }

  _createClass(RawDecoder, [{
    key: "decodeRect",
    value: function decodeRect(x, y, width, height, sock, display, depth) {
      if (width === 0 || height === 0) {
        return true;
      }

      if (this._lines === 0) {
        this._lines = height;
      }

      var pixelSize = depth == 8 ? 1 : 4;
      var bytesPerLine = width * pixelSize;

      if (sock.rQwait("RAW", bytesPerLine)) {
        return false;
      }

      var curY = y + (height - this._lines);
      var currHeight = Math.min(this._lines, Math.floor(sock.rQlen / bytesPerLine));
      var pixels = width * currHeight;
      var data = sock.rQ;
      var index = sock.rQi; // Convert data if needed

      if (depth == 8) {
        var newdata = new Uint8Array(pixels * 4);

        for (var i = 0; i < pixels; i++) {
          newdata[i * 4 + 0] = (data[index + i] >> 0 & 0x3) * 255 / 3;
          newdata[i * 4 + 1] = (data[index + i] >> 2 & 0x3) * 255 / 3;
          newdata[i * 4 + 2] = (data[index + i] >> 4 & 0x3) * 255 / 3;
          newdata[i * 4 + 3] = 255;
        }

        data = newdata;
        index = 0;
      } // Max sure the image is fully opaque


      for (var _i = 0; _i < pixels; _i++) {
        data[index + _i * 4 + 3] = 255;
      }

      display.blitImage(x, curY, width, currHeight, data, index);
      sock.rQskipBytes(currHeight * bytesPerLine);
      this._lines -= currHeight;

      if (this._lines > 0) {
        return false;
      }

      return true;
    }
  }]);

  return RawDecoder;
}();

exports["default"] = RawDecoder;