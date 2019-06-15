// include jquery if it's not define
if (typeof(jQuery) === "undefined") {
  window.jQuery = require("jquery");
  window.$ = window.jQuery;
}

var App = {
  init: function() {
    require("bootstrap/js/transition");
    require("bootstrap/js/collapse");
    require("bootstrap/js/dropdown");
    require("bootstrap/js/alert");
  }
};

$(document).ready(App.init);
