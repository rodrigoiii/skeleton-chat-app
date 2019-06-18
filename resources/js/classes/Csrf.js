function Csrf(token_name) {
  var _this = this;
  this.token_name = token_name || "_token";

  $.ajaxSetup({
    complete: function(jqXHR) {
      var new_token = jqXHR.getResponseHeader('X-CSRF-TOKEN');
      $('meta[name="'+_this.token_name+'"]').attr('content', new_token);
    }
  });
}

Csrf.prototype = {
  mergeWithToken: function(data) {
    var csrf = {};

    var token_el = $('meta[name="'+this.token_name+'"]');

    if (token_el.length > 0) {
      var token = JSON.parse(token_el.attr('content'));
      for (var key in token) {
        csrf[key] = token[key];
      }
    }

    return $.extend(csrf, data);
  }
};

module.exports = Csrf;
