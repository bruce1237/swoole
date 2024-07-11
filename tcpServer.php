<?php

// create sftp server and listen to 127.0.0.1 9501 port
$server = new Swoole\Server('127.0.0.1', 9501);

// monitor connect event
$server->on("Connect", function ($server, $fd) {
    echo "Client({$fd}): Connect \n";
});


// monitor receive
$server->on("Receive", function($server, $fd, $reactor_id, $data) {
    $server->send($fd, "Server: {$data} - {$reactor_id}");
});


// monitor discount 
$server->on("Close", function($server, $fd){
    echo "Client: closed; \n";
});


// start server
$server->start();