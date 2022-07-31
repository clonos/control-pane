"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var Log = _interopRequireWildcard(require("../util/logging.js"));

var _events = require("../util/events.js");

var KeyboardUtil = _interopRequireWildcard(require("./util.js"));

var _keysym = _interopRequireDefault(require("./keysym.js"));

var browser = _interopRequireWildcard(require("../util/browser.js"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _getRequireWildcardCache(nodeInterop) { if (typeof WeakMap !== "function") return null; var cacheBabelInterop = new WeakMap(); var cacheNodeInterop = new WeakMap(); return (_getRequireWildcardCache = function _getRequireWildcardCache(nodeInterop) { return nodeInterop ? cacheNodeInterop : cacheBabelInterop; })(nodeInterop); }

function _interopRequireWildcard(obj, nodeInterop) { if (!nodeInterop && obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(nodeInterop); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (key !== "default" && Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

//
// Keyboard event handler
//
var Keyboard = /*#__PURE__*/function () {
  function Keyboard(target) {
    _classCallCheck(this, Keyboard);

    this._target = target || null;
    this._keyDownList = {}; // List of depressed keys
    // (even if they are happy)

    this._altGrArmed = false; // Windows AltGr detection
    // keep these here so we can refer to them later

    this._eventHandlers = {
      'keyup': this._handleKeyUp.bind(this),
      'keydown': this._handleKeyDown.bind(this),
      'blur': this._allKeysUp.bind(this)
    }; // ===== EVENT HANDLERS =====

    this.onkeyevent = function () {}; // Handler for key press/release

  } // ===== PRIVATE METHODS =====


  _createClass(Keyboard, [{
    key: "_sendKeyEvent",
    value: function _sendKeyEvent(keysym, code, down) {
      if (down) {
        this._keyDownList[code] = keysym;
      } else {
        // Do we really think this key is down?
        if (!(code in this._keyDownList)) {
          return;
        }

        delete this._keyDownList[code];
      }

      Log.Debug("onkeyevent " + (down ? "down" : "up") + ", keysym: " + keysym, ", code: " + code);
      this.onkeyevent(keysym, code, down);
    }
  }, {
    key: "_getKeyCode",
    value: function _getKeyCode(e) {
      var code = KeyboardUtil.getKeycode(e);

      if (code !== 'Unidentified') {
        return code;
      } // Unstable, but we don't have anything else to go on


      if (e.keyCode) {
        // 229 is used for composition events
        if (e.keyCode !== 229) {
          return 'Platform' + e.keyCode;
        }
      } // A precursor to the final DOM3 standard. Unfortunately it
      // is not layout independent, so it is as bad as using keyCode


      if (e.keyIdentifier) {
        // Non-character key?
        if (e.keyIdentifier.substr(0, 2) !== 'U+') {
          return e.keyIdentifier;
        }

        var codepoint = parseInt(e.keyIdentifier.substr(2), 16);

        var _char = String.fromCharCode(codepoint).toUpperCase();

        return 'Platform' + _char.charCodeAt();
      }

      return 'Unidentified';
    }
  }, {
    key: "_handleKeyDown",
    value: function _handleKeyDown(e) {
      var code = this._getKeyCode(e);

      var keysym = KeyboardUtil.getKeysym(e); // Windows doesn't have a proper AltGr, but handles it using
      // fake Ctrl+Alt. However the remote end might not be Windows,
      // so we need to merge those in to a single AltGr event. We
      // detect this case by seeing the two key events directly after
      // each other with a very short time between them (<50ms).

      if (this._altGrArmed) {
        this._altGrArmed = false;
        clearTimeout(this._altGrTimeout);

        if (code === "AltRight" && e.timeStamp - this._altGrCtrlTime < 50) {
          // FIXME: We fail to detect this if either Ctrl key is
          //        first manually pressed as Windows then no
          //        longer sends the fake Ctrl down event. It
          //        does however happily send real Ctrl events
          //        even when AltGr is already down. Some
          //        browsers detect this for us though and set the
          //        key to "AltGraph".
          keysym = _keysym["default"].XK_ISO_Level3_Shift;
        } else {
          this._sendKeyEvent(_keysym["default"].XK_Control_L, "ControlLeft", true);
        }
      } // We cannot handle keys we cannot track, but we also need
      // to deal with virtual keyboards which omit key info


      if (code === 'Unidentified') {
        if (keysym) {
          // If it's a virtual keyboard then it should be
          // sufficient to just send press and release right
          // after each other
          this._sendKeyEvent(keysym, code, true);

          this._sendKeyEvent(keysym, code, false);
        }

        (0, _events.stopEvent)(e);
        return;
      } // Alt behaves more like AltGraph on macOS, so shuffle the
      // keys around a bit to make things more sane for the remote
      // server. This method is used by RealVNC and TigerVNC (and
      // possibly others).


      if (browser.isMac() || browser.isIOS()) {
        switch (keysym) {
          case _keysym["default"].XK_Super_L:
            keysym = _keysym["default"].XK_Alt_L;
            break;

          case _keysym["default"].XK_Super_R:
            keysym = _keysym["default"].XK_Super_L;
            break;

          case _keysym["default"].XK_Alt_L:
            keysym = _keysym["default"].XK_Mode_switch;
            break;

          case _keysym["default"].XK_Alt_R:
            keysym = _keysym["default"].XK_ISO_Level3_Shift;
            break;
        }
      } // Is this key already pressed? If so, then we must use the
      // same keysym or we'll confuse the server


      if (code in this._keyDownList) {
        keysym = this._keyDownList[code];
      } // macOS doesn't send proper key events for modifiers, only
      // state change events. That gets extra confusing for CapsLock
      // which toggles on each press, but not on release. So pretend
      // it was a quick press and release of the button.


      if ((browser.isMac() || browser.isIOS()) && code === 'CapsLock') {
        this._sendKeyEvent(_keysym["default"].XK_Caps_Lock, 'CapsLock', true);

        this._sendKeyEvent(_keysym["default"].XK_Caps_Lock, 'CapsLock', false);

        (0, _events.stopEvent)(e);
        return;
      } // Windows doesn't send proper key releases for a bunch of
      // Japanese IM keys so we have to fake the release right away


      var jpBadKeys = [_keysym["default"].XK_Zenkaku_Hankaku, _keysym["default"].XK_Eisu_toggle, _keysym["default"].XK_Katakana, _keysym["default"].XK_Hiragana, _keysym["default"].XK_Romaji];

      if (browser.isWindows() && jpBadKeys.includes(keysym)) {
        this._sendKeyEvent(keysym, code, true);

        this._sendKeyEvent(keysym, code, false);

        (0, _events.stopEvent)(e);
        return;
      }

      (0, _events.stopEvent)(e); // Possible start of AltGr sequence? (see above)

      if (code === "ControlLeft" && browser.isWindows() && !("ControlLeft" in this._keyDownList)) {
        this._altGrArmed = true;
        this._altGrTimeout = setTimeout(this._handleAltGrTimeout.bind(this), 100);
        this._altGrCtrlTime = e.timeStamp;
        return;
      }

      this._sendKeyEvent(keysym, code, true);
    }
  }, {
    key: "_handleKeyUp",
    value: function _handleKeyUp(e) {
      (0, _events.stopEvent)(e);

      var code = this._getKeyCode(e); // We can't get a release in the middle of an AltGr sequence, so
      // abort that detection


      if (this._altGrArmed) {
        this._altGrArmed = false;
        clearTimeout(this._altGrTimeout);

        this._sendKeyEvent(_keysym["default"].XK_Control_L, "ControlLeft", true);
      } // See comment in _handleKeyDown()


      if ((browser.isMac() || browser.isIOS()) && code === 'CapsLock') {
        this._sendKeyEvent(_keysym["default"].XK_Caps_Lock, 'CapsLock', true);

        this._sendKeyEvent(_keysym["default"].XK_Caps_Lock, 'CapsLock', false);

        return;
      }

      this._sendKeyEvent(this._keyDownList[code], code, false); // Windows has a rather nasty bug where it won't send key
      // release events for a Shift button if the other Shift is still
      // pressed


      if (browser.isWindows() && (code === 'ShiftLeft' || code === 'ShiftRight')) {
        if ('ShiftRight' in this._keyDownList) {
          this._sendKeyEvent(this._keyDownList['ShiftRight'], 'ShiftRight', false);
        }

        if ('ShiftLeft' in this._keyDownList) {
          this._sendKeyEvent(this._keyDownList['ShiftLeft'], 'ShiftLeft', false);
        }
      }
    }
  }, {
    key: "_handleAltGrTimeout",
    value: function _handleAltGrTimeout() {
      this._altGrArmed = false;
      clearTimeout(this._altGrTimeout);

      this._sendKeyEvent(_keysym["default"].XK_Control_L, "ControlLeft", true);
    }
  }, {
    key: "_allKeysUp",
    value: function _allKeysUp() {
      Log.Debug(">> Keyboard.allKeysUp");

      for (var code in this._keyDownList) {
        this._sendKeyEvent(this._keyDownList[code], code, false);
      }

      Log.Debug("<< Keyboard.allKeysUp");
    } // ===== PUBLIC METHODS =====

  }, {
    key: "grab",
    value: function grab() {
      //Log.Debug(">> Keyboard.grab");
      this._target.addEventListener('keydown', this._eventHandlers.keydown);

      this._target.addEventListener('keyup', this._eventHandlers.keyup); // Release (key up) if window loses focus


      window.addEventListener('blur', this._eventHandlers.blur); //Log.Debug("<< Keyboard.grab");
    }
  }, {
    key: "ungrab",
    value: function ungrab() {
      //Log.Debug(">> Keyboard.ungrab");
      this._target.removeEventListener('keydown', this._eventHandlers.keydown);

      this._target.removeEventListener('keyup', this._eventHandlers.keyup);

      window.removeEventListener('blur', this._eventHandlers.blur); // Release (key up) all keys that are in a down state

      this._allKeysUp(); //Log.Debug(">> Keyboard.ungrab");

    }
  }]);

  return Keyboard;
}();

exports["default"] = Keyboard;