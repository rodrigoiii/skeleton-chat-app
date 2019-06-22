var EventHandler = require("./EventHandler");

function Chat(config) {
  this.config = config;
}

Chat.prototype = {
  emitMessage: function(msg, errorCallback) {
    switch(this.webSocket.readyState) {
      case this.webSocket.CONNECTING:
        console.log("Connecting...");
        break;

      case this.webSocket.OPEN:
        this.webSocket.send(JSON.stringify(msg));
        break;

      case this.webSocket.CLOSING:
        console.log("Closing...");
        break;

      case this.webSocket.CLOSED:
        console.log("Closed!");

        if (typeof(errorCallback) !== "undefined") {
          errorCallback();
        } else {
          console.log("The server is disconnect.");
        }
        break;
    }
  },

  connect: function() {
    var _this = this;
    var eventHandler = new EventHandler();
    var webSocket = new WebSocket("ws://" + this.config.host + ":" + this.config.port + "?login_token=" + this.config.login_token);

    webSocket.onopen = function(e) {
      eventHandler.connected();
      this.send(JSON.stringify({event: eventHandler.asyncHandler.ON_CONNECTED}));
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

    this.webSocket = webSocket;
  }
};

module.exports = Chat;
