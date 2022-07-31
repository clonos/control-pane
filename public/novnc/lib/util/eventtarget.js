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
 */
var EventTargetMixin = /*#__PURE__*/function () {
  function EventTargetMixin() {
    _classCallCheck(this, EventTargetMixin);

    this._listeners = new Map();
  }

  _createClass(EventTargetMixin, [{
    key: "addEventListener",
    value: function addEventListener(type, callback) {
      if (!this._listeners.has(type)) {
        this._listeners.set(type, new Set());
      }

      this._listeners.get(type).add(callback);
    }
  }, {
    key: "removeEventListener",
    value: function removeEventListener(type, callback) {
      if (this._listeners.has(type)) {
        this._listeners.get(type)["delete"](callback);
      }
    }
  }, {
    key: "dispatchEvent",
    value: function dispatchEvent(event) {
      var _this = this;

      if (!this._listeners.has(event.type)) {
        return true;
      }

      this._listeners.get(event.type).forEach(function (callback) {
        return callback.call(_this, event);
      });

      return !event.defaultPrevented;
    }
  }]);

  return EventTargetMixin;
}();

exports["default"] = EventTargetMixin;