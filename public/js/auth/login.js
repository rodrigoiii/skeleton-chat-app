/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ 6:
/***/ (function(module, exports, __webpack_require__) {

var JvBs3 = __webpack_require__(7);

var Login = {
  init: function() {
    Login.initValidation();
  },

  initValidation: function() {
    var jvBs3 = new JvBs3("#login-form", {
      errorElement: "span",
      errorClass: "help-block",

      rules: {
        email: {
          required: true,
          email: true
        },
        password: {
          required: true
        }
      }
    });

    jvBs3.setExceptFields(["email", "password"]);
    jvBs3.validate();
  }
};

$(document).ready(Login.init);


/***/ }),

/***/ 7:
/***/ (function(module, exports) {

// Jquery Validation Bootstrap 3
function JvBs3(form_selector, options) {
  this.form_selector = form_selector;
  this.options = options;

  this.icon_ok = "glyphicon-ok";
  this.icon_error = "glyphicon-remove";

  this.except_fields = [];
}

JvBs3.prototype.setIconOk = function(icon_ok) {
  this.icon_ok = icon_ok;
};

JvBs3.prototype.setIconError = function(icon_error) {
  this.icon_error = icon_error;
};

JvBs3.prototype.setExceptFields = function(fields) {
  this.except_fields = fields;
};

JvBs3.prototype.clearError = function(element) {
  var form_group = $(element).closest('.form-group');

  if (form_group.hasClass('has-error')) {
    form_group.removeClass('has-error');
  }

  if ($("." + this.icon_error, form_group).length > 0) {
    $("." + this.icon_error, form_group).remove();
  }
};

JvBs3.prototype.clearOk = function(element) {
  var form_group = $(element).closest('.form-group');

  if (form_group.hasClass('has-success')) {
    form_group.removeClass('has-success');
  }

  if ($("." + this.icon_success, form_group).length > 0) {
    $("." + this.icon_success, form_group).remove();
  }
};

JvBs3.prototype.validate = function() {
  var _this = this;

  $(this.form_selector).validate($.extend({
    // error
    highlight: function(element, errorClass, successClass) {
      _this.clearOk(element);

      var form_group = $(element).closest('.form-group');
      var feedback = form_group.find('.form-control-feedback');

      // append has-error class in form-group class
      form_group.addClass('has-error');

      // if no feedback element
      if (feedback.length === 0) {
        $(element).after('<span class="glyphicon ' + _this.icon_error + ' form-control-feedback"></span>');
      } else if (!feedback.hasClass(_this.icon_error)) { // if no icon error
        feedback.addClass(_this.icon_error);
      }
    },

    // ok
    unhighlight: function(element, errorClass, successClass) {
      _this.clearError(element);

      var field_name = $(element).attr('name');

      if (_this.except_fields.indexOf(field_name) === -1) {
        var form_group = $(element).closest('.form-group');
        var feedback = form_group.find('.form-control-feedback');

        // append has-success class in form-group class
        form_group.addClass('has-success');

        // if no feedback element
        if (feedback.length === 0) {
          $(element).after('<span class="glyphicon ' + _this.icon_ok + ' form-control-feedback"></span>');
        } else if (!feedback.hasClass(_this.icon_ok)) { // if no icon ok
          feedback.addClass(_this.icon_ok);
        }
      }
    }
  }, this.options));
};

module.exports = JvBs3;


/***/ })

/******/ });