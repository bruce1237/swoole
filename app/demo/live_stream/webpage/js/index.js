

var wsUrl = "ws://localhost:9501";
var webSocket = new WebSocket(wsUrl);

// onOpen
webSocket.onopen = function (evt) {
    console.log("Event object:", evt);
    console.log("webSocket--connected");
    webSocket.send("hi, server");
}

// onMessage
webSocket.onmessage = function (evt) {
    console.log("received from Server:" + evt.data);
    // webSocket.send("hi, received");
    obj = JSON.parse(evt.data);
    section = obj.section;
    updates = obj.content;


    if ($("#" + section + "Head").val() == "") {
        $("#" + section + "Head").html(section);
    }
    $("#" + section).append(updates + "<br />");
}

// onClose
webSocket.onclose = function (evt) {
    console.log("Closed");
}

webSocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};