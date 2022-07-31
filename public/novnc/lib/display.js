"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var Log = _interopRequireWildcard(require("./util/logging.js"));

var _base = _interopRequireDefault(require("./base64.js"));

var _int = require("./util/int.js");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function _getRequireWildcardCache(nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }

function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

var Display = /*#__PURE__*/function () {
  function Display(target) {
    _classCallCheck(this, Display);

    this._drawCtx = null;
    this._renderQ = []; // queue drawing actions for in-oder rendering

    this._flushing = false; // the full frame buffer (logical canvas) size

    this._fbWidth = 0;
    this._fbHeight = 0;
    this._prevDrawStyle = "";
    Log.Debug(">> Display.constructor"); // The visible canvas

    this._target = target;

    if (!this._target) {
      throw new Error("Target must be set");
    }

    if (typeof this._target === 'string') {
      throw new Error('target must be a DOM element');
    }

    if (!this._target.getContext) {
      throw new Error("no getContext method");
    }

    this._targetCtx = this._target.getContext('2d'); // the visible canvas viewport (i.e. what actually gets seen)

    this._viewportLoc = {
      'x': 0,
      'y': 0,
      'w': this._target.width,
      'h': this._target.height
    }; // The hidden canvas, where we do the actual rendering

    this._backbuffer = document.createElement('canvas');
    this._drawCtx = this._backbuffer.getContext('2d');
    this._damageBounds = {
      left: 0,
      top: 0,
      right: this._backbuffer.width,
      bottom: this._backbuffer.height
    };
    Log.Debug("User Agent: " + navigator.userAgent);
    Log.Debug("<< Display.constructor"); // ===== PROPERTIES =====

    this._scale = 1.0;
    this._clipViewport = false; // ===== EVENT HANDLERS =====

    this.onflush = function () {}; // A flush request has finished

  } // ===== PROPERTIES =====


  _createClass(Display, [{
    key: "scale",
    get: function get() {
      return this._scale;
    },
    set: function set(scale) {
      this._rescale(scale);
    }
  }, {
    key: "clipViewport",
    get: function get() {
      return this._clipViewport;
    },
    set: function set(viewport) {
      this._clipViewport = viewport; // May need to readjust the viewport dimensions

      var vp = this._viewportLoc;
      this.viewportChangeSize(vp.w, vp.h);
      this.viewportChangePos(0, 0);
    }
  }, {
    key: "width",
    get: function get() {
      return this._fbWidth;
    }
  }, {
    key: "height",
    get: function get() {
      return this._fbHeight;
    } // ===== PUBLIC METHODS =====

  }, {
    key: "viewportChangePos",
    value: function viewportChangePos(deltaX, deltaY) {
      var vp = this._viewportLoc;
      deltaX = Math.floor(deltaX);
      deltaY = Math.floor(deltaY);

      if (!this._clipViewport) {
        deltaX = -vp.w; // clamped later of out of bounds

        deltaY = -vp.h;
      }

      var vx2 = vp.x + vp.w - 1;
      var vy2 = vp.y + vp.h - 1; // Position change

      if (deltaX < 0 && vp.x + deltaX < 0) {
        deltaX = -vp.x;
      }

      if (vx2 + deltaX >= this._fbWidth) {
        deltaX -= vx2 + deltaX - this._fbWidth + 1;
      }

      if (vp.y + deltaY < 0) {
        deltaY = -vp.y;
      }

      if (vy2 + deltaY >= this._fbHeight) {
        deltaY -= vy2 + deltaY - this._fbHeight + 1;
      }

      if (deltaX === 0 && deltaY === 0) {
        return;
      }

      Log.Debug("viewportChange deltaX: " + deltaX + ", deltaY: " + deltaY);
      vp.x += deltaX;
      vp.y += deltaY;

      this._damage(vp.x, vp.y, vp.w, vp.h);

      this.flip();
    }
  }, {
    key: "viewportChangeSize",
    value: function viewportChangeSize(width, height) {
      if (!this._clipViewport || typeof width === "undefined" || typeof height === "undefined") {
        Log.Debug("Setting viewport to full display region");
        width = this._fbWidth;
        height = this._fbHeight;
      }

      width = Math.floor(width);
      height = Math.floor(height);

      if (width > this._fbWidth) {
        width = this._fbWidth;
      }

      if (height > this._fbHeight) {
        height = this._fbHeight;
      }

      var vp = this._viewportLoc;

      if (vp.w !== width || vp.h !== height) {
        vp.w = width;
        vp.h = height;
        var canvas = this._target;
        canvas.width = width;
        canvas.height = height; // The position might need to be updated if we've grown

        this.viewportChangePos(0, 0);

        this._damage(vp.x, vp.y, vp.w, vp.h);

        this.flip(); // Update the visible size of the target canvas

        this._rescale(this._scale);
      }
    }
  }, {
    key: "absX",
    value: function absX(x) {
      if (this._scale === 0) {
        return 0;
      }

      return (0, _int.toSigned32bit)(x / this._scale + this._viewportLoc.x);
    }
  }, {
    key: "absY",
    value: function absY(y) {
      if (this._scale === 0) {
        return 0;
      }

      return (0, _int.toSigned32bit)(y / this._scale + this._viewportLoc.y);
    }
  }, {
    key: "resize",
    value: function resize(width, height) {
      this._prevDrawStyle = "";
      this._fbWidth = width;
      this._fbHeight = height;
      var canvas = this._backbuffer;

      if (canvas.width !== width || canvas.height !== height) {
        // We have to save the canvas data since changing the size will clear it
        var saveImg = null;

        if (canvas.width > 0 && canvas.height > 0) {
          saveImg = this._drawCtx.getImageData(0, 0, canvas.width, canvas.height);
        }

        if (canvas.width !== width) {
          canvas.width = width;
        }

        if (canvas.height !== height) {
          canvas.height = height;
        }

        if (saveImg) {
          this._drawCtx.putImageData(saveImg, 0, 0);
        }
      } // Readjust the viewport as it may be incorrectly sized
      // and positioned


      var vp = this._viewportLoc;
      this.viewportChangeSize(vp.w, vp.h);
      this.viewportChangePos(0, 0);
    } // Track what parts of the visible canvas that need updating

  }, {
    key: "_damage",
    value: function _damage(x, y, w, h) {
      if (x < this._damageBounds.left) {
        this._damageBounds.left = x;
      }

      if (y < this._damageBounds.top) {
        this._damageBounds.top = y;
      }

      if (x + w > this._damageBounds.right) {
        this._damageBounds.right = x + w;
      }

      if (y + h > this._damageBounds.bottom) {
        this._damageBounds.bottom = y + h;
      }
    } // Update the visible canvas with the contents of the
    // rendering canvas

  }, {
    key: "flip",
    value: function flip(fromQueue) {
      if (this._renderQ.length !== 0 && !fromQueue) {
        this._renderQPush({
          'type': 'flip'
        });
      } else {
        var x = this._damageBounds.left;
        var y = this._damageBounds.top;
        var w = this._damageBounds.right - x;
        var h = this._damageBounds.bottom - y;
        var vx = x - this._viewportLoc.x;
        var vy = y - this._viewportLoc.y;

        if (vx < 0) {
          w += vx;
          x -= vx;
          vx = 0;
        }

        if (vy < 0) {
          h += vy;
          y -= vy;
          vy = 0;
        }

        if (vx + w > this._viewportLoc.w) {
          w = this._viewportLoc.w - vx;
        }

        if (vy + h > this._viewportLoc.h) {
          h = this._viewportLoc.h - vy;
        }

        if (w > 0 && h > 0) {
          // FIXME: We may need to disable image smoothing here
          //        as well (see copyImage()), but we haven't
          //        noticed any problem yet.
          this._targetCtx.drawImage(this._backbuffer, x, y, w, h, vx, vy, w, h);
        }

        this._damageBounds.left = this._damageBounds.top = 65535;
        this._damageBounds.right = this._damageBounds.bottom = 0;
      }
    }
  }, {
    key: "pending",
    value: function pending() {
      return this._renderQ.length > 0;
    }
  }, {
    key: "flush",
    value: function flush() {
      if (this._renderQ.length === 0) {
        this.onflush();
      } else {
        this._flushing = true;
      }
    }
  }, {
    key: "fillRect",
    value: function fillRect(x, y, width, height, color, fromQueue) {
      if (this._renderQ.length !== 0 && !fromQueue) {
        this._renderQPush({
          'type': 'fill',
          'x': x,
          'y': y,
          'width': width,
          'height': height,
          'color': color
        });
      } else {
        this._setFillColor(color);

        this._drawCtx.fillRect(x, y, width, height);

        this._damage(x, y, width, height);
      }
    }
  }, {
    key: "copyImage",
    value: function copyImage(oldX, oldY, newX, newY, w, h, fromQueue) {
      if (this._renderQ.length !== 0 && !fromQueue) {
        this._renderQPush({
          'type': 'copy',
          'oldX': oldX,
          'oldY': oldY,
          'x': newX,
          'y': newY,
          'width': w,
          'height': h
        });
      } else {
        // Due to this bug among others [1] we need to disable the image-smoothing to
        // avoid getting a blur effect when copying data.
        //
        // 1. https://bugzilla.mozilla.org/show_bug.cgi?id=1194719
        //
        // We need to set these every time since all properties are reset
        // when the the size is changed
        this._drawCtx.mozImageSmoothingEnabled = false;
        this._drawCtx.webkitImageSmoothingEnabled = false;
        this._drawCtx.msImageSmoothingEnabled = false;
        this._drawCtx.imageSmoothingEnabled = false;

        this._drawCtx.drawImage(this._backbuffer, oldX, oldY, w, h, newX, newY, w, h);

        this._damage(newX, newY, w, h);
      }
    }
  }, {
    key: "imageRect",
    value: function imageRect(x, y, width, height, mime, arr) {
      /* The internal logic cannot handle empty images, so bail early */
      if (width === 0 || height === 0) {
        return;
      }

      var img = new Image();
      img.src = "data: " + mime + ";base64," + _base["default"].encode(arr);

      this._renderQPush({
        'type': 'img',
        'img': img,
        'x': x,
        'y': y,
        'width': width,
        'height': height
      });
    }
  }, {
    key: "blitImage",
    value: function blitImage(x, y, width, height, arr, offset, fromQueue) {
      if (this._renderQ.length !== 0 && !fromQueue) {
        // NB(directxman12): it's technically more performant here to use preallocated arrays,
        // but it's a lot of extra work for not a lot of payoff -- if we're using the render queue,
        // this probably isn't getting called *nearly* as much
        var newArr = new Uint8Array(width * height * 4);
        newArr.set(new Uint8Array(arr.buffer, 0, newArr.length));

        this._renderQPush({
          'type': 'blit',
          'data': newArr,
          'x': x,
          'y': y,
          'width': width,
          'height': height
        });
      } else {
        // NB(directxman12): arr must be an Type Array view
        var data = new Uint8ClampedArray(arr.buffer, arr.byteOffset + offset, width * height * 4);
        var img = new ImageData(data, width, height);

        this._drawCtx.putImageData(img, x, y);

        this._damage(x, y, width, height);
      }
    }
  }, {
    key: "drawImage",
    value: function drawImage(img, x, y) {
      this._drawCtx.drawImage(img, x, y);

      this._damage(x, y, img.width, img.height);
    }
  }, {
    key: "autoscale",
    value: function autoscale(containerWidth, containerHeight) {
      var scaleRatio;

      if (containerWidth === 0 || containerHeight === 0) {
        scaleRatio = 0;
      } else {
        var vp = this._viewportLoc;
        var targetAspectRatio = containerWidth / containerHeight;
        var fbAspectRatio = vp.w / vp.h;

        if (fbAspectRatio >= targetAspectRatio) {
          scaleRatio = containerWidth / vp.w;
        } else {
          scaleRatio = containerHeight / vp.h;
        }
      }

      this._rescale(scaleRatio);
    } // ===== PRIVATE METHODS =====

  }, {
    key: "_rescale",
    value: function _rescale(factor) {
      this._scale = factor;
      var vp = this._viewportLoc; // NB(directxman12): If you set the width directly, or set the
      //                   style width to a number, the canvas is cleared.
      //                   However, if you set the style width to a string
      //                   ('NNNpx'), the canvas is scaled without clearing.

      var width = factor * vp.w + 'px';
      var height = factor * vp.h + 'px';

      if (this._target.style.width !== width || this._target.style.height !== height) {
        this._target.style.width = width;
        this._target.style.height = height;
      }
    }
  }, {
    key: "_setFillColor",
    value: function _setFillColor(color) {
      var newStyle = 'rgb(' + color[0] + ',' + color[1] + ',' + color[2] + ')';

      if (newStyle !== this._prevDrawStyle) {
        this._drawCtx.fillStyle = newStyle;
        this._prevDrawStyle = newStyle;
      }
    }
  }, {
    key: "_renderQPush",
    value: function _renderQPush(action) {
      this._renderQ.push(action);

      if (this._renderQ.length === 1) {
        // If this can be rendered immediately it will be, otherwise
        // the scanner will wait for the relevant event
        this._scanRenderQ();
      }
    }
  }, {
    key: "_resumeRenderQ",
    value: function _resumeRenderQ() {
      // "this" is the object that is ready, not the
      // display object
      this.removeEventListener('load', this._noVNCDisplay._resumeRenderQ);

      this._noVNCDisplay._scanRenderQ();
    }
  }, {
    key: "_scanRenderQ",
    value: function _scanRenderQ() {
      var ready = true;

      while (ready && this._renderQ.length > 0) {
        var a = this._renderQ[0];

        switch (a.type) {
          case 'flip':
            this.flip(true);
            break;

          case 'copy':
            this.copyImage(a.oldX, a.oldY, a.x, a.y, a.width, a.height, true);
            break;

          case 'fill':
            this.fillRect(a.x, a.y, a.width, a.height, a.color, true);
            break;

          case 'blit':
            this.blitImage(a.x, a.y, a.width, a.height, a.data, 0, true);
            break;

          case 'img':
            if (a.img.complete) {
              if (a.img.width !== a.width || a.img.height !== a.height) {
                Log.Error("Decoded image has incorrect dimensions. Got " + a.img.width + "x" + a.img.height + ". Expected " + a.width + "x" + a.height + ".");
                return;
              }

              this.drawImage(a.img, a.x, a.y);
            } else {
              a.img._noVNCDisplay = this;
              a.img.addEventListener('load', this._resumeRenderQ); // We need to wait for this image to 'load'
              // to keep things in-order

              ready = false;
            }

            break;
        }

        if (ready) {
          this._renderQ.shift();
        }
      }

      if (this._renderQ.length === 0 && this._flushing) {
        this._flushing = false;
        this.onflush();
      }
    }
  }]);

  return Display;
}();

exports["default"] = Display;