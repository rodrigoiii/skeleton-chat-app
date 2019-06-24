var _ = require("underscore");
var Helper = require("./Helper");

// Asynchronous handlers
function AsyncHandler() {
  //
}

AsyncHandler.onConnected = function(data) {
  var emitter_id = data.emitter_id;
  var contacts_el = $('#contacts');

  var status_el = $('.contact[data-id="'+emitter_id+'"] .contact-status', contacts_el);

  if (!status_el.hasClass("online")) {
    status_el.addClass("online");
  }
};

AsyncHandler.onDisconnected = function(data) {
  var emitter_id = data.emitter_id;
  var contacts_el = $('#contacts');

  var status_el = $('.contact[data-id="'+emitter_id+'"] .contact-status', contacts_el);

  if (status_el.hasClass("online")) {
    status_el.removeClass("online");
  }
};

AsyncHandler.onTyping = function(data) {
  var userTyping = data.from;
  var activeContact = Helper.getActiveContact();

  if (activeContact.id == userTyping.id) {
    var tmpl = _.template($('#message-tmpl').html());

    $('#messages ul').append(tmpl({
      sent: false,
      picture: userTyping.picture,
      message: "...",
      classAdded: "typing-type"
    }));
  }
};

AsyncHandler.onStopTyping = function(data) {
  var userTyping = data.from;
  var activeContact = Helper.getActiveContact();

  if (activeContact.id == userTyping.id) {
    var tmpl = _.template($('#message-tmpl').html());

    var typingTypeEl = $('#messages ul li:last.typing-type');
    if (typingTypeEl.length > 0) {
      typingTypeEl.remove();
    }
  }
};

module.exports = AsyncHandler;
