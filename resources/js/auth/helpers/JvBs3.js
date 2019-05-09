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
