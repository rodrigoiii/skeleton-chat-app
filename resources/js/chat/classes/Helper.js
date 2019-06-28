var Helper = {
  scrollMessage: function(height, animate, callback) {
    var bottom = height || $('#messages').prop('scrollHeight');
    animate = animate || true;

    if (animate) {
      $('#messages').animate({ scrollTop: bottom }, "fast", callback);
    } else {
      $('#messages').scrollTop(bottom);
    }
  },

  getAuthInfo: function() {
    var el = $('#profile');

    return {
      id: el.data("id"),
      picture: $('#profile-img').attr("src"),
      fullname: $(".wrap .fullname", el).text()
    };
  },

  hasContact: function() {
    return !$('#contacts ul .contact').hasClass("no-contacts");
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
