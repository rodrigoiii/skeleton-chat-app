require("bootstrap/js/transition");
require("bootstrap/js/modal");
require("bootstrap/js/dropdown");
require("bootstrap/js/button");
var _ = require("underscore");
var Chat = require("./classes/Chat");
var Helper = require("./classes/Helper");
var ChatApi = require("./classes/Api");
var AsyncHandler = require("./classes/AsyncHandler");

/**
 * global object
 * - chatObj
 */
var ChatApp = {
  is_typing: false,
  get_message_batch: 1,

  chat: null,

  init: function() {
    window.chatApiObj = new ChatApi(chatObj.user.login_token);

    ChatApp.chat = new Chat({
      host: chatObj.config.host,
      port: chatObj.config.port,
      login_token: chatObj.user.login_token
    });
    ChatApp.chat.connect();

    $('#search :input[name="filter-contacts"]').keyup(ChatApp.onFilterContacts);
    $('#contacts').on("click", ".contact", ChatApp.activateContact);
    $('#addcontact').click(ChatApp.onAddContact);
    $('body').on("keyup", '.add-contact-modal :input[name="search_contact"]', _.throttle(ChatApp.onSearchingContact, 800));
    $('body').on('click', ".add-contact-modal .send-contact-request", ChatApp.onSendContactRequest);

    $('#notification-menu').on("click", ".accept-request", ChatApp.onAcceptRequest);

    $("#notification-dropdown").on("shown.bs.dropdown", ChatApp.onReadNotification);
    $(document).on('click', '#notification-dropdown .dropdown-menu', function (e) {
      e.stopPropagation();
    });

    $('#input-message').on("keyup", ChatApp.onTyping);
    $('#input-message').on('keyup', _.debounce(ChatApp.onStopTyping, 1500));
    $('#send-message').click(_.throttle(ChatApp.onSendMessage, 800));

    $('#messages').scroll(ChatApp.onLoadMoreMessages);

    // activate first contact if exist
    if ($('#contacts .contact:first').length > 0) {
      $('#contacts .contact:first').click();
    }
  },

  onFilterContacts: function() {
    var keyword = $(this).val().toLowerCase();

    $('#contacts .contact').filter(function() {
        return $(".wrap .meta .name", this).text()
          .toLowerCase()
          .search(keyword) !== -1;
      }).css('display', "block");

    $('#contacts .contact').filter(function() {
        return $(".wrap .meta .name", this).text()
          .toLowerCase()
          .search(keyword) === -1;
      }).css('display', "none");
  },

  activateContact: function() {
    var _this = this;

    if (Helper.hasContact()) {
      $('#contacts .contact').removeClass("active");
      $(this).addClass("active");

      var activeContact = Helper.getActiveContact();
      var authInfo = Helper.getAuthInfo();

      var activeContactEl = $('#content .active-contact');
      $("img", activeContactEl).attr('src', activeContact.picture);
      $("p", activeContactEl).text(activeContact.fullname);

      var tmpl = _.template($('#message-tmpl').html());
      chatApiObj.getConversation(activeContact.id, function(response) {
        if (response.success) {
          var conversation = response.conversation;

          $('#messages ul').html("");
          _.each(conversation, function(convo) {
            $('#messages ul').append(tmpl({
              sent: convo.sender.id == authInfo.id,
              picture: convo.sender.picture,
              message: convo.message
            }));
          });

          ChatApp.get_message_batch = 1; // reset message batch
          Helper.scrollMessage();

          // clear input message
          $('#input-message').val("");

          // umn - unread message number
          var umnEl = $('.wrap .meta .name .unread-message-number', $(_this));

          if (parseInt(umnEl.data("number")) > 0) {
            chatApiObj.readMessage(activeContact.id, function(readMessageResponse) {
              if (readMessageResponse.success) {
                umnEl.data("number", 0);
                umnEl.text("");
              }
            });
          }
        }
      });
    }
  },

  onAddContact: function() {
    var tmpl = _.template($('#add-contact-tmpl').html());

    var box = bootbox.dialog({
      title: "Add contact",
      message: tmpl(),
      className: "add-contact-modal"
    });

    box.on("shown.bs.modal", function() {
      $('.add-contact-modal :input[name="search_contact"]').focus();
    });
  },

  onSearchingContact: function() {
    var keyword = $(this).val();
    var tmpl = _.template($('#search-contact-result-tmpl').html());

    chatApiObj.searchContacts(keyword, function(result) {
      if (result.success) {
        $('.add-contact-modal table tbody').html(tmpl({
          result_users: result.users
        }));
      } else {
        console.error("Error: Cannot search contact this time. Please try again later");
      }
    });
  },

  onSendContactRequest: function() {
    var _this = this;

    var authInfo = Helper.getAuthInfo();
    var to_id = $(this).data("user-id");
    var full_name = $(this).data("full-name");
    var picture = $(this).data("picture");

    $(this).prop('disabled', true);
    $(this).button('loading');

    chatApiObj.sendContactRequest(to_id, function(response) {
      if (response.success) {
        $(_this).fadeOut(function() {
          $(this).parent().html('<span class="label label-success">Successfully sent request.</span>');
          $(this).remove();

          var tmpl = _.template($('#notification-tmpl').html());

          $('#notification-menu').prepend(tmpl({
            user_id: to_id,
            type: AsyncHandler.NOTIFICATION_SEND_REQUEST,
            picture: picture,
            notif_message: "You send request to " + full_name,
            enabled_accept_button: false
          }));

          // remove empty notification if exist
          if ($('#notification-menu .empty').length > 0) {
            $('#notification-menu .empty').remove();
          }

          ChatApp.chat.emitter.sendRequest(to_id);
        });
      }
    });
  },

  onAcceptRequest: function() {
    var _this = this;
    var requester_id = $(this).data("user-id");

    $(this).prop('disabled', true);
    $(this).button('loading');

    var tmpl = _.template($('#contact-list-tmpl').html());

    var contactsEl = $('#contacts ul');

    chatApiObj.acceptRequest(requester_id, function(response) {
      if (response.success) {
        var requester = response.requester;

        $(_this).fadeOut(function() {
          $(this).closest(".item").find(".item-info p").html(response.notif_message);
          $(this).remove();

          // prepend contact
          contactsEl.prepend(tmpl({
            user_id: requester.id,
            online: requester.online,
            picture: requester.picture,
            fullname: requester.full_name,
            unread_message_number: 0,
            preview_message: ""
          }));

          // remove no contacts if exist
          if ($('.no-contacts', contactsEl).length > 0) {
            $('.no-contacts', contactsEl).remove();
          }

          ChatApp.chat.emitter.acceptRequest(requester.id);
        });
      }
    });
  },

  onReadNotification: function() {
    var badge_el = $('a .notif-number', $(this));
    var notif_num = badge_el.text();

    if (!isNaN(notif_num)) {
      if (parseInt(notif_num) > 0) {
        chatApiObj.readNotification(function(response) {
          if (response.success) {
            badge_el.remove();
          }
        });
      }
    }
  },

  onTyping: function(e) {
    var ENTER_KEYCODE = 13;

    if (e.which == ENTER_KEYCODE) {
      $('#send-message').click();
      return false;
    }

    if (!ChatApp.is_typing) {
      ChatApp.is_typing = true;

      var activeContact = Helper.getActiveContact();
      ChatApp.chat.emitter.typing(activeContact.id);
    }
  },

  onStopTyping: function() {
    ChatApp.is_typing = false;

    var activeContact = Helper.getActiveContact();
    ChatApp.chat.emitter.stopTyping(activeContact.id);
  },

  onSendMessage: function() {
    var sendMessageBtnEl = $('#send-message');

    var input_el = $('#input-message');
    var message = input_el.val().trim();

    var before = function() {
      if (message !== "") {
        if (message.length <= 1000) {
          $('#send-message').prop("disabled", true);
          $('#send-message').html('<span class="glyphicon glyphicon-refresh rotating"></span>');

          load();
        } else {
          alert("You cannot send message more than 1000 characters.");
        }
      }
    };

    var load = function() {
      input_el.val("");

      var activeContact = Helper.getActiveContact();

      var tmpl = _.template($('#message-tmpl').html());
      var content_el = $('#content');

      chatApiObj.sendMessage(activeContact.id, message, function(response) {
        if (response.success) {
          $('.messages ul', content_el).append(tmpl({
            picture: chatObj.user.picture,
            message: message,
            sent: true
          }));

          Helper.scrollMessage();

          ChatApp.chat.emitter.sendMessage(activeContact.id, message);
        }

        after();
      });
    };

    var after = function() {
      $('#send-message').prop("disabled", false);
      $('#send-message').html('<i class="glyphicon glyphicon-send" aria-hidden="true"></i>');
    };

    before();
  },

  onLoadMoreMessages: function() {
    var _this = this;

    if ($(this).scrollTop() === 0) {
      if (Helper.canLoadMoreMessage()) {
        var authInfo = Helper.getAuthInfo();
        var activeContact = Helper.getActiveContact();

        $('ul', $(this)).prepend('<li class="load-more text-center">Loading...</li>');

        chatApiObj.getMessagesByBatch(activeContact.id, ChatApp.get_message_batch, function(response) {
          if (response.success) {
            var conversation = response.conversation;
            var tmpl = _.template($('#message-tmpl').html());
            var str = "";

            // delete load more message
            $("ul li:first-child.load-more", $(_this)).remove();

            _.each(conversation, function(convo) {
              str += tmpl({
                sent: authInfo.id == convo.sender.id,
                picture: convo.sender.picture,
                message: convo.message
              });
            });
            $("ul", $(_this)).prepend(str);

            // add no more message if no conversation remaining
            if (conversation.length === 0) {
              $("ul", $(_this)).prepend('<li class="no-more text-center">No more message.</li>');
            } else {
              var ONE_LINE_HEIGHT = 38;
              Helper.scrollMessage(conversation.length * ONE_LINE_HEIGHT, false);
            }

            ChatApp.get_message_batch++;
          }
        });
      }
    }
  }
};

$(document).ready(ChatApp.init);
