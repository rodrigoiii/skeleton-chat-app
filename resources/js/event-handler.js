var EventHandler = {
  onConnectionEstablish: function(data) {
    if (Helper.isTokenValid(data.token) && data.success) {
      var contact_el = $('#contacts .contact[data-id="'+data.auth_user_id+'"]');

      if (!$('.contact-status', contact_el).hasClass("online")) {
        $('.contact-status', contact_el).addClass("online");
      }
    }
  },

  onDisconnect: function(data) {
    if (Helper.isTokenValid(data.token) && data.success) {
      var contact_el = $('#contacts .contact[data-id="'+data.auth_user_id+'"]');

      if ($('.contact-status', contact_el).hasClass("online")) {
        $('.contact-status', contact_el).removeClass("online");
      }
    }
  },

  onTyping: function(data) {
    if (Helper.isTokenValid(data.token)) {
      var user = data.chatting_from;

      if (Helper.getActiveContactId() == user.id) {
        var tmpl = _.template($('#typing-tmpl').html());

        $('.messages ul').append(tmpl({
          picture: user.picture
        }));
      }

      $('#contacts .contact[data-id="'+user.id+'"] .meta .preview').text("...");

      Helper.scrollMessage();
    }
  },

  onStopTyping: function(data) {
    if (Helper.isTokenValid(data.token)) {
      $('.messages ul li.typing').remove();

      var last_message = $('.messages li:last-child p').text();
      $('#contacts .contact[data-id="'+data.chatting_from_id+'"] .meta .preview').text(last_message);
    }
  },

  onSendMessage: function(data) {
    if (Helper.isTokenValid(data.token)) {
      var message = data.message;
      var sender = message.sender;
      var unread_number = data.unread_number;

      if (Helper.getActiveContactId() == sender.id) {
        if ($('.messages ul').length === 0) {
          $('.messages').html('<ul></ul>');
        }

        if ($('.messages').hasClass('no-message')) {
          $('.messages').removeClass('no-message');
        }

        var tmpl = _.template($('#message-item-tmpl').html());
        $('.messages ul').append(tmpl({
          is_sender: false,
          picture: sender.picture,
          message: message.message
        }));

        Helper.setUnreadNumber(sender.id, unread_number);

        var contact_el = $('#contacts .contact[data-id="'+sender.id+'"]');
        $('.meta .preview', contact_el).text(message.message);

        Helper.scrollMessage();
      }
    }
  },

  onSendContactRequest: function(data) {
    if (Helper.isTokenValid(data.token)) {
      Chat.chatApi.getUnreadNumber(function(unreadNumberResponse) {
        if (unreadNumberResponse.success) {
          Helper.updateNotificationNumber(unreadNumberResponse.unread_number);
        }
      });
    }
  }
};
