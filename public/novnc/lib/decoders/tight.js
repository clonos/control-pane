"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var Log = _interopRequireWildcard(require("../util/logging.js"));

var _inflator = _interopRequireDefault(require("../inflator.js"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function _getRequireWildcardCache(nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }

function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

var TightDecoder = /*#__PURE__*/function () {
  function TightDecoder() {
    _classCallCheck(this, TightDecoder);

    this._ctl = null;
    this._filter = null;
    this._numColors = 0;
    this._palette = new Uint8Array(1024); // 256 * 4 (max palette size * max bytes-per-pixel)

    this._len = 0;
    this._zlibs = [];

    for (var i = 0; i < 4; i++) {
      this._zlibs[i] = new _inflator["default"]();
    }
  }

  _createClass(TightDecoder, [{
    key: "decodeRect",
    value: function decodeRect(x, y, width, height, sock, display, depth) {
      if (this._ctl === null) {
        if (sock.rQwait("TIGHT compression-control", 1)) {
          return false;
        }

        this._ctl = sock.rQshift8(); // Reset streams if the server requests it

        for (var i = 0; i < 4; i++) {
          if (this._ctl >> i & 1) {
            this._zlibs[i].reset();

            Log.Info("Reset zlib stream " + i);
          }
        } // Figure out filter


        this._ctl = this._ctl >> 4;
      }

      var ret;

      if (this._ctl === 0x08) {
        ret = this._fillRect(x, y, width, height, sock, display, depth);
      } else if (this._ctl === 0x09) {
        ret = this._jpegRect(x, y, width, height, sock, display, depth);
      } else if (this._ctl === 0x0A) {
        ret = this._pngRect(x, y, width, height, sock, display, depth);
      } else if ((this._ctl & 0x08) == 0) {
        ret = this._basicRect(this._ctl, x, y, width, height, sock, display, depth);
      } else {
        throw new Error("Illegal tight compression received (ctl: " + this._ctl + ")");
      }

      if (ret) {
        this._ctl = null;
      }

      return ret;
    }
  }, {
    key: "_fillRect",
    value: function _fillRect(x, y, width, height, sock, display, depth) {
      if (sock.rQwait("TIGHT", 3)) {
        return false;
      }

      var rQi = sock.rQi;
      var rQ = sock.rQ;
      display.fillRect(x, y, width, height, [rQ[rQi], rQ[rQi + 1], rQ[rQi + 2]], false);
      sock.rQskipBytes(3);
      return true;
    }
  }, {
    key: "_jpegRect",
    value: function _jpegRect(x, y, width, height, sock, display, depth) {
      var data = this._readData(sock);

      if (data === null) {
        return false;
      }

      display.imageRect(x, y, width, height, "image/jpeg", data);
      return true;
    }
  }, {
    key: "_pngRect",
    value: function _pngRect(x, y, width, height, sock, display, depth) {
      throw new Error("PNG received in standard Tight rect");
    }
  }, {
    key: "_basicRect",
    value: function _basicRect(ctl, x, y, width, height, sock, display, depth) {
      if (this._filter === null) {
        if (ctl & 0x4) {
          if (sock.rQwait("TIGHT", 1)) {
            return false;
          }

          this._filter = sock.rQshift8();
        } else {
          // Implicit CopyFilter
          this._filter = 0;
        }
      }

      var streamId = ctl & 0x3;
      var ret;

      switch (this._filter) {
        case 0:
          // CopyFilter
          ret = this._copyFilter(streamId, x, y, width, height, sock, display, depth);
          break;

        case 1:
          // PaletteFilter
          ret = this._paletteFilter(streamId, x, y, width, height, sock, display, depth);
          break;

        case 2:
          // GradientFilter
          ret = this._gradientFilter(streamId, x, y, width, height, sock, display, depth);
          break;

        default:
          throw new Error("Illegal tight filter received (ctl: " + this._filter + ")");
      }

      if (ret) {
        this._filter = null;
      }

      return ret;
    }
  }, {
    key: "_copyFilter",
    value: function _copyFilter(streamId, x, y, width, height, sock, display, depth) {
      var uncompressedSize = width * height * 3;
      var data;

      if (uncompressedSize === 0) {
        return true;
      }

      if (uncompressedSize < 12) {
        if (sock.rQwait("TIGHT", uncompressedSize)) {
          return false;
        }

        data = sock.rQshiftBytes(uncompressedSize);
      } else {
        data = this._readData(sock);

        if (data === null) {
          return false;
        }

        this._zlibs[streamId].setInput(data);

        data = this._zlibs[streamId].inflate(uncompressedSize);

        this._zlibs[streamId].setInput(null);
      }

      var rgbx = new Uint8Array(width * height * 4);

      for (var i = 0, j = 0; i < width * height * 4; i += 4, j += 3) {
        rgbx[i] = data[j];
        rgbx[i + 1] = data[j + 1];
        rgbx[i + 2] = data[j + 2];
        rgbx[i + 3] = 255; // Alpha
      }

      display.blitImage(x, y, width, height, rgbx, 0, false);
      return true;
    }
  }, {
    key: "_paletteFilter",
    value: function _paletteFilter(streamId, x, y, width, height, sock, display, depth) {
      if (this._numColors === 0) {
        if (sock.rQwait("TIGHT palette", 1)) {
          return false;
        }

        var numColors = sock.rQpeek8() + 1;
        var paletteSize = numColors * 3;

        if (sock.rQwait("TIGHT palette", 1 + paletteSize)) {
          return false;
        }

        this._numColors = numColors;
        sock.rQskipBytes(1);
        sock.rQshiftTo(this._palette, paletteSize);
      }

      var bpp = this._numColors <= 2 ? 1 : 8;
      var rowSize = Math.floor((width * bpp + 7) / 8);
      var uncompressedSize = rowSize * height;
      var data;

      if (uncompressedSize === 0) {
        return true;
      }

      if (uncompressedSize < 12) {
        if (sock.rQwait("TIGHT", uncompressedSize)) {
          return false;
        }

        data = sock.rQshiftBytes(uncompressedSize);
      } else {
        data = this._readData(sock);

        if (data === null) {
          return false;
        }

        this._zlibs[streamId].setInput(data);

        data = this._zlibs[streamId].inflate(uncompressedSize);

        this._zlibs[streamId].setInput(null);
      } // Convert indexed (palette based) image data to RGB


      if (this._numColors == 2) {
        this._monoRect(x, y, width, height, data, this._palette, display);
      } else {
        this._paletteRect(x, y, width, height, data, this._palette, display);
      }

      this._numColors = 0;
      return true;
    }
  }, {
    key: "_monoRect",
    value: function _monoRect(x, y, width, height, data, palette, display) {
      // Convert indexed (palette based) image data to RGB
      // TODO: reduce number of calculations inside loop
      var dest = this._getScratchBuffer(width * height * 4);

      var w = Math.floor((width + 7) / 8);
      var w1 = Math.floor(width / 8);

      for (var _y = 0; _y < height; _y++) {
        var dp = void 0,
            sp = void 0,
            _x = void 0;

        for (_x = 0; _x < w1; _x++) {
          for (var b = 7; b >= 0; b--) {
            dp = (_y * width + _x * 8 + 7 - b) * 4;
            sp = (data[_y * w + _x] >> b & 1) * 3;
            dest[dp] = palette[sp];
            dest[dp + 1] = palette[sp + 1];
            dest[dp + 2] = palette[sp + 2];
            dest[dp + 3] = 255;
          }
        }

        for (var _b = 7; _b >= 8 - width % 8; _b--) {
          dp = (_y * width + _x * 8 + 7 - _b) * 4;
          sp = (data[_y * w + _x] >> _b & 1) * 3;
          dest[dp] = palette[sp];
          dest[dp + 1] = palette[sp + 1];
          dest[dp + 2] = palette[sp + 2];
          dest[dp + 3] = 255;
        }
      }

      display.blitImage(x, y, width, height, dest, 0, false);
    }
  }, {
    key: "_paletteRect",
    value: function _paletteRect(x, y, width, height, data, palette, display) {
      // Convert indexed (palette based) image data to RGB
      var dest = this._getScratchBuffer(width * height * 4);

      var total = width * height * 4;

      for (var i = 0, j = 0; i < total; i += 4, j++) {
        var sp = data[j] * 3;
        dest[i] = palette[sp];
        dest[i + 1] = palette[sp + 1];
        dest[i + 2] = palette[sp + 2];
        dest[i + 3] = 255;
      }

      display.blitImage(x, y, width, height, dest, 0, false);
    }
  }, {
    key: "_gradientFilter",
    value: function _gradientFilter(streamId, x, y, width, height, sock, display, depth) {
      throw new Error("Gradient filter not implemented");
    }
  }, {
    key: "_readData",
    value: function _readData(sock) {
      if (this._len === 0) {
        if (sock.rQwait("TIGHT", 3)) {
          return null;
        }

        var _byte;

        _byte = sock.rQshift8();
        this._len = _byte & 0x7f;

        if (_byte & 0x80) {
          _byte = sock.rQshift8();
          this._len |= (_byte & 0x7f) << 7;

          if (_byte & 0x80) {
            _byte = sock.rQshift8();
            this._len |= _byte << 14;
          }
        }
      }

      if (sock.rQwait("TIGHT", this._len)) {
        return null;
      }

      var data = sock.rQshiftBytes(this._len);
      this._len = 0;
      return data;
    }
  }, {
    key: "_getScratchBuffer",
    value: function _getScratchBuffer(size) {
      if (!this._scratchBuffer || this._scratchBuffer.length < size) {
        this._scratchBuffer = new Uint8Array(size);
      }

      return this._scratchBuffer;
    }
  }]);

  return TightDecoder;
}();

exports["default"] = TightDecoder;