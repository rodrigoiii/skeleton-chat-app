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

var _ = require("underscore");

var Chat = {
  init: function() {
    $('#addcontact').click(Chat.onAddContact);
  },

  onAddContact: function() {
    var tmpl = _.template($('#add-contact-tmpl').html());

    bootbox.dialog({
      title: "Add contact",
      message: tmpl()
    });
  }
};

$(document).ready(Chat.init);
