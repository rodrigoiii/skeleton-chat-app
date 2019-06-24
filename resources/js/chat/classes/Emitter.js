function Emitter(webSocket) {
  this.webSocket = webSocket;
}

Emitter.ON_CONNECTED = "onConnected";
// Emitter.ON_DISCONNECTED = "onDisconnected"; // exist already in the server side, no need for mapping
Emitter.ON_TYPING = "onTyping";
Emitter.ON_STOP_TYPING = "onStopTyping";

Emitter.prototype = {
  emit: function(msg, errorCallback) { // interface
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

  typing: function(to_id) {
    var msg = {
      event: Emitter.ON_TYPING,
      to_id: to_id
    };

    this.emit(msg);
  },

  stopTyping: function(to_id) {
    var msg = {
      event: Emitter.ON_STOP_TYPING,
      to_id: to_id
    };

    this.emit(msg);
  },
};

module.exports = Emitter;
