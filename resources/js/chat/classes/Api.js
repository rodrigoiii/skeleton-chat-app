// require jquery
var Csrf = require("./../../classes/Csrf");

function Api(login_token) {
  this.login_token = login_token;

  this.csrf = new Csrf();
}

Api.prototype = {
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
      login_token: this.login_token,
      _METHOD: "PUT"
    };

    $.post("/api/accept-request", this.csrf.mergeWithToken(params), callback);
  },

  readNotification: function(callback) {
    var params = {
      login_token: this.login_token,
      _METHOD: "PUT"
    };

    $.post("/api/read-notification", this.csrf.mergeWithToken(params), callback);
  },

  sendMessage: function(to_id, message, callback) {
    var params = {
      login_token: this.login_token,
      message: message
    };
    $.post("/api/send-message/" + to_id, this.csrf.mergeWithToken(params), callback);
  },

  getConversation: function(to_id, callback) {
    var query_string = "?login_token=" + this.login_token;
    $.get("/api/conversation/" + to_id + query_string, callback);
  },

  getMessagesByBatch: function(to_id, batch, callback) {
    var query_string = "?login_token=" + this.login_token +
                       "&batch=" + batch;
    $.get("/api/get-messages-by-batch/" + to_id + query_string, callback);
  },

  readMessage: function(to_id, callback) {
    var params = {
      login_token: this.login_token,
      _METHOD: "PUT"
    };
    $.post("/api/read-message/" + to_id, this.csrf.mergeWithToken(params), callback);
  }
};

module.exports = Api;
