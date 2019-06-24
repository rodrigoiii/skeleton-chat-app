var Helper = {
  scrollMessage: function(height, callback) {
    var bottom = height || $('.messages').prop('scrollHeight');
    $('.messages').animate({ scrollTop: bottom }, "fast", callback);
  },

  getAuthInfo: function() {
    var el = $('#profile');

    return {
      id: el.data("id"),
      picture: $('#profile-img').attr("src"),
      fullname: $(".wrap .fullname", el).text()
    };
  },

  getActiveContact: function() {
    var el = $('#contacts .contact.active');

    return {
      el: el,
      id: el.data("id"),
      picture: $(".wrap img", el).attr("src"),
      fullname: $(".wrap .meta .name .fullname", el).text()
    };
  },

  getContactElById: function(id) {
    return $('#contacts .contact[data-id="'+id+'"]');
  },

  canLoadMoreMessage: function () {
    var firstMessageEl = $('#messages ul li:first-child');
    return !firstMessageEl.hasClass("no-more") && !firstMessageEl.hasClass("load-more");
  }
};

module.exports = Helper;
