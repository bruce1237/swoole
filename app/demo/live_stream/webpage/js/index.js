$(document).ready(function () {

    // live stream webSocket

    var wsUrl = "ws://192.168.1.157:9501";
    var webSocket = new WebSocket(wsUrl);

    // onOpen
    webSocket.onopen = function (evt) {
        console.log("Event object:", evt);
        console.log("LiveStream--connected");

    }

    // onMessage
    webSocket.onmessage = function (evt) {
        console.log("received from Server:" + evt.data);
        // webSocket.send("hi, received");
        obj = JSON.parse(evt.data);
        game = obj.game;
        section = obj.section;
        updates = obj.content;


        if ($("#game").html() == "") {
            $("#game").html(game);
        }

        if ($("#gameName").val() == "") {
            $("#" + section + "Head").html("Game: " + section);
            $("#gameName").val(game);
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



    $("#chatSend").click(function () {
        

        chatMsg = $("#chatMsg").val();
        section = $("#section").val();

        $.ajax({
            url: "./chatRoom/sendChat",
            type: "POST",
            data: {
                game: $("#gameName").val(),
                chatMsg: chatMsg,
            },
            success: function (response) {

            }

        });

    });



    // ChatRoom webSocket

    var wsUrl = "ws://192.168.1.157:9502";
    var webSocket = new WebSocket(wsUrl);

    // onOpen
    webSocket.onopen = function (evt) {
        console.log("Event object:", evt);
        console.log("ChatRoom--connected");

    }

    // onMessage
    webSocket.onmessage = function (evt) {
        console.log("received from Server:" + evt.data);
        // webSocket.send("hi, received");
        obj = JSON.parse(evt.data);
        game = obj.game;
        msg = obj.msg;


        if ($("#chatRoomName").html() == "") {
            $("#chatRoomName").html("ChatRoom: " + game);
        }

        $("#chatRoom").append(msg + "<br />");

        
    }

    // onClose
    webSocket.onclose = function (evt) {
        console.log("Closed");
    }

    webSocket.onerror = function (evt, e) {
        console.log('Error occured: ' + evt.data);
    };

    // load chatHistory
    
    
});

