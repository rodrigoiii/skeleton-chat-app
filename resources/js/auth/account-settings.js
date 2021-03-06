/**
 * global variable
 * - account_setting
 *   - auth_user
 *     - email
 */

var JvBs3 = require("./helpers/JvBs3");

var AccountSetting = {
  init: function() {
    AccountSetting.initValidation();
  },

  initValidation: function() {
    var jvBs3 = new JvBs3("#account-settings-form", {
      errorElement: "span",
      errorClass: "help-block",

      rules: {
        picture: {
          accept: "image/gif,image/jpeg,image/png",
          file_size: {
            max_size: 5000000, // 5mb
          }
        },
        first_name: {
          required: true,
          regex: /^[a-zA-Z\s]+$/i
        },
        last_name: {
          required: true,
          regex: /^[a-zA-Z\s]+$/i
        },
        current_password: {
          required: {
            depends: function(element) {
              var form = $('#account-settings-form');
              return $(':input[name="new_password"]', form).val() !== "" || $(':input[name="confirm_new_password"]', form).val() !== "";
            }
          }
        },
        new_password: {
          required: {
            depends: function(element) {
              var form = $('#account-settings-form');
              return $(':input[name="current_password"]', form).val() !== "" || $(':input[name="confirm_new_password"]', form).val() !== "";
            }
          },
          password_strength: true
        },
        confirm_new_password: {
          required: {
            depends: function(element) {
              var form = $('#account-settings-form');
              return $(':input[name="current_password"]', form).val() !== "" || $(':input[name="new_password"]', form).val() !== "";
            }
          },
          equalTo: '#account-settings-form :input[name="new_password"]'
        }
      },

      messages: {
        first_name: {
          regex: "Please enter only letters."
        },
        last_name: {
          regex: "Please enter only letters."
        },
        confirm_password: {
          equalTo: "Password and confirm password do not match"
        }
      }
    });

    jvBs3.setExceptFields(["picture", "current_password", "new_password", "confirm_new_password"]);
    jvBs3.validate();
  }
};

$(document).ready(AccountSetting.init);
