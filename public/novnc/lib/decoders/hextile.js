"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var Log = _interopRequireWildcard(require("../util/logging.js"));

function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function _getRequireWildcardCache(nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }

function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

var HextileDecoder = /*#__PURE__*/function () {
  function HextileDecoder() {
    _classCallCheck(this, HextileDecoder);

    this._tiles = 0;
    this._lastsubencoding = 0;
    this._tileBuffer = new Uint8Array(16 * 16 * 4);
  }

  _createClass(HextileDecoder, [{
    key: "decodeRect",
    value: function decodeRect(x, y, width, height, sock, display, depth) {
      if (this._tiles === 0) {
        this._tilesX = Math.ceil(width / 16);
        this._tilesY = Math.ceil(height / 16);
        this._totalTiles = this._tilesX * this._tilesY;
        this._tiles = this._totalTiles;
      }

      while (this._tiles > 0) {
        var bytes = 1;

        if (sock.rQwait("HEXTILE", bytes)) {
          return false;
        }

        var rQ = sock.rQ;
        var rQi = sock.rQi;
        var subencoding = rQ[rQi]; // Peek

        if (subencoding > 30) {
          // Raw
          throw new Error("Illegal hextile subencoding (subencoding: " + subencoding + ")");
        }

        var currTile = this._totalTiles - this._tiles;
        var tileX = currTile % this._tilesX;
        var tileY = Math.floor(currTile / this._tilesX);
        var tx = x + tileX * 16;
        var ty = y + tileY * 16;
        var tw = Math.min(16, x + width - tx);
        var th = Math.min(16, y + height - ty); // Figure out how much we are expecting

        if (subencoding & 0x01) {
          // Raw
          bytes += tw * th * 4;
        } else {
          if (subencoding & 0x02) {
            // Background
            bytes += 4;
          }

          if (subencoding & 0x04) {
            // Foreground
            bytes += 4;
          }

          if (subencoding & 0x08) {
            // AnySubrects
            bytes++; // Since we aren't shifting it off

            if (sock.rQwait("HEXTILE", bytes)) {
              return false;
            }

            var subrects = rQ[rQi + bytes - 1]; // Peek

            if (subencoding & 0x10) {
              // SubrectsColoured
              bytes += subrects * (4 + 2);
            } else {
              bytes += subrects * 2;
            }
          }
        }

        if (sock.rQwait("HEXTILE", bytes)) {
          return false;
        } // We know the encoding and have a whole tile


        rQi++;

        if (subencoding === 0) {
          if (this._lastsubencoding & 0x01) {
            // Weird: ignore blanks are RAW
            Log.Debug("     Ignoring blank after RAW");
          } else {
            display.fillRect(tx, ty, tw, th, this._background);
          }
        } else if (subencoding & 0x01) {
          // Raw
          var pixels = tw * th; // Max sure the image is fully opaque

          for (var i = 0; i < pixels; i++) {
            rQ[rQi + i * 4 + 3] = 255;
          }

          display.blitImage(tx, ty, tw, th, rQ, rQi);
          rQi += bytes - 1;
        } else {
          if (subencoding & 0x02) {
            // Background
            this._background = [rQ[rQi], rQ[rQi + 1], rQ[rQi + 2], rQ[rQi + 3]];
            rQi += 4;
          }

          if (subencoding & 0x04) {
            // Foreground
            this._foreground = [rQ[rQi], rQ[rQi + 1], rQ[rQi + 2], rQ[rQi + 3]];
            rQi += 4;
          }

          this._startTile(tx, ty, tw, th, this._background);

          if (subencoding & 0x08) {
            // AnySubrects
            var _subrects = rQ[rQi];
            rQi++;

            for (var s = 0; s < _subrects; s++) {
              var color = void 0;

              if (subencoding & 0x10) {
                // SubrectsColoured
                color = [rQ[rQi], rQ[rQi + 1], rQ[rQi + 2], rQ[rQi + 3]];
                rQi += 4;
              } else {
                color = this._foreground;
              }

              var xy = rQ[rQi];
              rQi++;
              var sx = xy >> 4;
              var sy = xy & 0x0f;
              var wh = rQ[rQi];
              rQi++;
              var sw = (wh >> 4) + 1;
              var sh = (wh & 0x0f) + 1;

              this._subTile(sx, sy, sw, sh, color);
            }
          }

          this._finishTile(display);
        }

        sock.rQi = rQi;
        this._lastsubencoding = subencoding;
        this._tiles--;
      }

      return true;
    } // start updating a tile

  }, {
    key: "_startTile",
    value: function _startTile(x, y, width, height, color) {
      this._tileX = x;
      this._tileY = y;
      this._tileW = width;
      this._tileH = height;
      var red = color[0];
      var green = color[1];
      var blue = color[2];
      var data = this._tileBuffer;

      for (var i = 0; i < width * height * 4; i += 4) {
        data[i] = red;
        data[i + 1] = green;
        data[i + 2] = blue;
        data[i + 3] = 255;
      }
    } // update sub-rectangle of the current tile

  }, {
    key: "_subTile",
    value: function _subTile(x, y, w, h, color) {
      var red = color[0];
      var green = color[1];
      var blue = color[2];
      var xend = x + w;
      var yend = y + h;
      var data = this._tileBuffer;
      var width = this._tileW;

      for (var j = y; j < yend; j++) {
        for (var i = x; i < xend; i++) {
          var p = (i + j * width) * 4;
          data[p] = red;
          data[p + 1] = green;
          data[p + 2] = blue;
          data[p + 3] = 255;
        }
      }
    } // draw the current tile to the screen

  }, {
    key: "_finishTile",
    value: function _finishTile(display) {
      display.blitImage(this._tileX, this._tileY, this._tileW, this._tileH, this._tileBuffer, 0);
    }
  }]);

  return HextileDecoder;
}();

exports["default"] = HextileDecoder;