// require jquery
var Csrf = require("./../Csrf");

function ChatApi(login_token) {
  this.login_token = login_token;

  this.csrf = new Csrf();
}

ChatApi.prototype = {
  searchContacts: function(keyword, callback) {
    var query_string = "?login_token=" + this.login_token +
                       "&keyword=" + keyword;
    $.get("/api/search-contacts" + query_string, callback);
  },

  sendContactRequest: function(to_id, callback) {
    var params = {
      to_id: to_id,
      login_token: this.login_token
    };

    $.post("/api/send-contact-request", this.csrf.mergeWithToken(params), callback);
  },

  acceptRequest: function(from_id, callback) {
    var params = {
      from_id: from_id,
      login_token: this.login_token
    };

    $.post("/api/accept-request", this.csrf.mergeWithToken(params), callback);
  },

  readNotification: function(callback) {
    var params = {
      login_token: this.login_token
    };

    $.post("/api/read-notification", this.csrf.mergeWithToken(params), callback);
  }
};

module.exports = ChatApi;
