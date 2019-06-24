var EventHandler = require("./EventHandler");
var Emitter = require("./Emitter");

function Chat(config) {
  this.config = config;
}

Chat.prototype = {
  connect: function() {
    var _this = this;
    var eventHandler = new EventHandler();
    var webSocket = new WebSocket("ws://" + this.config.host + ":" + this.config.port + "?login_token=" + this.config.login_token);

    webSocket.onopen = function(e) {
      eventHandler.connected();
      this.send(JSON.stringify({event: Emitter.ON_CONNECTED}));
    };

    webSocket.onclose = function(e) {
      webSocket = null;

      eventHandler.disconnected();
      eventHandler.reconnect(_this);
    };

    webSocket.onmessage = function(e) {
      var parse_data = JSON.parse(e.data);
      var event = parse_data.event;
      var token = parse_data.receiver_token;

      delete parse_data.event;
      delete parse_data.receiver_token;

      // check first if the receiver token is valid
      if (token !== null) {
        if (_this.config.login_token === token) {
          console.log(event, parse_data);
          eventHandler.asyncHandler[event](parse_data);

          return;
        }
      }

      console.log("Error: Receiver token " + token + " is invalid.");
    };

    this.emitter = new Emitter(webSocket);
  }
};

module.exports = Chat;
