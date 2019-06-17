function ChatApi(login_token) {
  this.login_token = login_token;
}

ChatApi.prototype = {
  searchContacts: function(keyword, callback) {
    var query_string = "?login_token=" + this.login_token +
                       "&keyword=" + keyword;
    $.get("/api/search-contacts" + query_string, callback);
  }
};

module.exports = ChatApi;
