var _ = require("underscore");
var Helper = require("./Helper");

// Asynchronous handlers
function AsyncHandler() {
  //
}

AsyncHandler.NOTIFICATION_SEND_REQUEST = "send-request";
AsyncHandler.NOTIFICATION_ACCEPT_REQUEST = "accept-request";

AsyncHandler.prototype = {
  onConnected: function(data) {
    var emitter_id = data.emitter_id;
    var contacts_el = $('#contacts');

    var status_el = $('.contact[data-id="'+emitter_id+'"] .contact-status', contacts_el);

    if (!status_el.hasClass("online")) {
      status_el.addClass("online");
    }
  },

  onDisconnected: function(data) {
    var emitter_id = data.emitter_id;
    var contacts_el = $('#contacts');

    var status_el = $('.contact[data-id="'+emitter_id+'"] .contact-status', contacts_el);

    if (status_el.hasClass("online")) {
      status_el.removeClass("online");
    }
  },

  onSendRequest: function(data) {
    var from = data.from;
    var notifMenuEl = $('#notification-menu');

    if ($('[data-user-id="'+from.id+'"][data-type="'+AsyncHandler.NOTIFICATION_SEND_REQUEST+'"]', notifMenuEl).length === 0) {
      var tmpl = _.template($('#notification-tmpl').html());

      $('#notification-menu').prepend(tmpl({
        user_id: from.id,
        type: AsyncHandler.NOTIFICATION_SEND_REQUEST,
        picture: from.picture,
        notif_message: data.notif_message,
        enabled_accept_button: true
      }));

      var notifBellEl = $('#notification-dropdown a');
      if ($('.notif-number', notifBellEl).length > 0) {
        var num = $('.notif-number', notifBellEl).text();

        if (!isNaN(num)) {
          $('.notif-number', notifBellEl).text(parseInt(num) + 1);
        }
      } else {
        notifBellEl.append('<span class="badge notif-number">1</span>');
      }

      // remove empty notification if exist
      if ($('#notification-menu .empty').length > 0) {
        $('#notification-menu .empty').remove();
      }
    }
  },

  onAcceptRequest: function(data) {
    var accepter = data.accepter;
    var notifMenuEl = $('#notification-menu');

    if ($('[data-user-id="'+accepter.id+'"][data-type="'+AsyncHandler.NOTIFICATION_ACCEPT_REQUEST+'"]', notifMenuEl).length === 0) {
      var notifTmpl = _.template($('#notification-tmpl').html());

      $('#notification-menu').prepend(notifTmpl({
        user_id: accepter.id,
        type: AsyncHandler.NOTIFICATION_ACCEPT_REQUEST,
        picture: accepter.picture,
        notif_message: data.notif_message,
        enabled_accept_button: false
      }));

      var notifBellEl = $('#notification-dropdown a');
      if ($('.notif-number', notifBellEl).length > 0) {
        var num = $('.notif-number', notifBellEl).text();

        if (!isNaN(num)) {
          $('.notif-number', notifBellEl).text(parseInt(num) + 1);
        }
      } else {
        notifBellEl.append('<span class="badge notif-number">1</span>');
      }

      // remove empty notification if exist
      if ($('#notification-menu .empty').length > 0) {
        $('#notification-menu .empty').remove();
      }
    }

    // remove no type request notification if exist
    var noTypeMenuEl = $('[data-user-id="'+accepter.id+'"][data-type="'+AsyncHandler.NOTIFICATION_SEND_REQUEST+'"]', notifMenuEl);
    if (noTypeMenuEl.length > 0) {
      noTypeMenuEl.remove();
    }

    // prepend contact
    var contactsEl = $('#contacts ul');
    if ($('.contact[data-id="'+accepter.id+'"]', contactsEl).length === 0) {
      var contactListTmpl = _.template($('#contact-list-tmpl').html());
      contactsEl.prepend(contactListTmpl({
        user_id: accepter.id,
        online: true,
        picture: accepter.picture,
        fullname: accepter.full_name,
        unread_message_number: 0,
        preview_message: ""
      }));
    }

    // remove no contacts if exist
    if ($('.no-contacts', contactsEl).length > 0) {
      $('.no-contacts', contactsEl).remove();
    }
  },

  onTyping: function(data) {
    var userTyping = data.from;

    var msgListEl = $('#messages ul');

    if ($('.typing-type', msgListEl).length === 0) {
      var activeContact = Helper.getActiveContact();

      if (activeContact.id == userTyping.id) {
        var tmpl = _.template($('#message-tmpl').html());

        msgListEl.append(tmpl({
          sent: false,
          picture: userTyping.picture,
          message: "...",
          classAdded: "typing-type"
        }));
      }
    }
  },

  onStopTyping: function(data) {
    var userTyping = data.from;

    var msgListEl = $('#messages ul');

    if ($('.typing-type', msgListEl).length > 0)
    {
      var activeContact = Helper.getActiveContact();

      if (activeContact.id == userTyping.id) {
        $('.typing-type', msgListEl).remove();
      }
    }
  },

  onSendMessage: function(data) {
    var sender = data.from;
    var activeContact = Helper.getActiveContact();

    if (activeContact.id == sender.id) {
      var typingTypeEl = $('#messages ul li:last.typing-type');
      if (typingTypeEl.length > 0) {
        typingTypeEl.remove();
      }

      var tmpl = _.template($('#message-tmpl').html());

      $('#messages ul').append(tmpl({
        sent: false,
        picture: sender.picture,
        message: sender.message
      }));
    }

    // update unread message
    // umn - unread message number
    var umnEl = $('.wrap .meta .name .unread-message-number', Helper.getContactElById(sender.id));
    umnEl.data("number", sender.unread_message_number);
    umnEl.text("(" + sender.unread_message_number + ")");
  }
};

module.exports = AsyncHandler;
