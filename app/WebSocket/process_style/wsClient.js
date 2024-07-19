const WebSocket = require('ws');
    var wsServer = 'ws://127.0.0.1:9501';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    websocket.send("Hello from client");
    console.log("Connected to WebSocket server.");
};


websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};
