function Emitter(eventHandler) {
  var config = eventHandler.config;
  var webSocket = new WebSocket("ws://" + config.host + ":" + config.port + "?login_token=" + config.login_token);

  webSocket.onopen = function(e) {
    eventHandler.connected();
    this.send(JSON.stringify({ event: Emitter.ON_CONNECTION_ESTABLISH }));
  };

  webSocket.onclose = function(e) {
    webSocket = null;
    eventHandler.disconnected();
  };

  webSocket.onmessage = function(e) {
    var parse_data = JSON.parse(e.data);
    var event = parse_data.event;
    delete parse_data.event;

    console.log(event, parse_data);
    eventHandler[event](parse_data);
  };

  this.webSocket = webSocket;
}

Emitter.ON_CONNECTION_ESTABLISH = "onConnectionEstablish";

Emitter.prototype.emitMessage = function(msg, errorCallback) {
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
};

module.exports = Emitter;
