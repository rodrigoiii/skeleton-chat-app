// Asynchronous handlers
function AsyncHandler() {
  //
}

AsyncHandler.ON_CONNECTED = "onConnected";
// AsyncHandler.ON_DISCONNECTED = "onDisconnected"; // exist already in the server side, no need for mapping
AsyncHandler.ON_TYPING = "onTyping";

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

};

module.exports = AsyncHandler;
