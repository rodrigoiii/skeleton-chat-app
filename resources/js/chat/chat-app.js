// $(document).ready(function($) {
//   $(".messages").animate({ scrollTop: $(document).height() }, "fast");

//   function newMessage() {
//       message = $(".message-input input").val();
//       if($.trim(message) == '') {
//           return false;
//       }
//       $('<li class="sent"><img src="http://emilcarlsson.se/assets/mikeross.png" alt="" /><p>' + message + '</p></li>').appendTo($('.messages ul'));
//       $('.message-input input').val(null);
//       $('.contact.active .preview').html('<span>You: </span>' + message);
//       $(".messages").animate({ scrollTop: $(document).height() }, "fast");
//   }

//   $('.submit').click(function() {
//     newMessage();
//   });

//   $(window).on('keydown', function(e) {
//     if (e.which == 13) {
//       newMessage();
//       return false;
//     }
//   });
// });
require("bootstrap/js/transition");
require("bootstrap/js/modal");
require("bootstrap/js/dropdown");
require("bootstrap/js/button");
var _ = require("underscore");
var Chat = require("./classes/Chat");
var ChatApi = require("./../classes/Chat/Api");

/**
 * global object
 * - chatObj
 */
var ChatApp = {
  init: function() {
    window.chatApiObj = new ChatApi(chatObj.login_token);

    // var eventHandler = new EventHandler({
    //   host: chatObj.config.host,
    //   port: chatObj.config.port,
    //   login_token: chatObj.login_token
    // });
    // eventHandler.connect();
    var chat = new Chat({
      host: chatObj.config.host,
      port: chatObj.config.port,
      login_token: chatObj.login_token
    });
    chat.connect();

    $('#search :input[name="filter-contacts"]').keyup(ChatApp.onFilterContacts);
    $('#addcontact').click(ChatApp.onAddContact);
    $('body').on("keyup", '.add-contact-modal :input[name="search_contact"]', _.throttle(ChatApp.onSearchingContact, 800));
    $('body').on('click', ".add-contact-modal .send-contact-request", ChatApp.onSendContactRequest);

    $('#notification-menu .accept-request').click(ChatApp.onAcceptRequest);

    $("#notification-dropdown").on("shown.bs.dropdown", ChatApp.onReadNotification);
    $(document).on('click', '#notification-dropdown .dropdown-menu', function (e) {
      e.stopPropagation();
    });
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

    var to_id = $(this).data("user-id");

    $(this).prop('disabled', true);
    $(this).button('loading');

    chatApiObj.sendContactRequest(to_id, function(response) {
      if (response.success) {
        $(_this).fadeOut(function() {
          $(this).parent().html('<span class="label label-success">Successfully sent request.</span>');
          $(this).remove();
        });
      }
    });
  },

  onAcceptRequest: function() {
    var _this = this;
    var from_id = $(this).data("from-id");

    $(this).prop('disabled', true);
    $(this).button('loading');

    chatApiObj.acceptRequest(from_id, function(response) {
      if (response.success) {
        $(_this).fadeOut(function() {
          $(this).closest(".item").find(".item-info p").html(response.notif_message);
          $(this).remove();
        });
      }
    });
  },

  onReadNotification: function() {
    var badge_el = $('a span', $(this));
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
  }
};

$(document).ready(ChatApp.init);
