var asyncHandler = require("./AsyncHandler");

function EventHandler() {
  //
}

EventHandler.prototype = {
  connected: function() { // interface
    if ($('.reconnecting-container').is(":visible")) {
      $('.reconnecting-container').hide();
    }
  },

  disconnected: function() { // interface
    $('.reconnecting-container').html("Disconnected!").show();
  },

  /**
   * @param  Chat chat
   * @return void
   */
  reconnect: function(chat) { // interface
    var countdown = 5; // seconds
    var time = setInterval(function () {
      if (countdown !== 0) {
        $('.reconnecting-container').html("Reconnecting... " + countdown).show();
        countdown -= 1;
      } else {
        chat.connect();
        clearInterval(time);
      }
    }, 1000);
  },

  // Asynchronous handlers
  asyncHandler: new asyncHandler()
};

module.exports = EventHandler;
