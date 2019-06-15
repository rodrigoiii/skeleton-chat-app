var JvBs3 = require("./helpers/JvBs3");

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
