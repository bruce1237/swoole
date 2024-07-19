<?php

// create sftp server and listen to 127.0.0.1 9501 port
$server = new Swoole\Server('127.0.0.1', 9501);

// config server
$server->set([
    "worker_num" => 2, // 1-4 times of cpu core
    "max_request" => 10, // based on the memory
]);



// monitor connect event
/**
 * $server: swoole server
 * $fd: connected client ID
 * $reactor_id: thread
 */
$server->on("Connect", function ($server, $fd, $reactor_id) {
    echo "Client({$fd}): thread:({$reactor_id}) - Connect \n";
});


// monitor receive
$server->on("Receive", function($server, $fd, $reactor_id, $data) {
    echo "server received: ({$data}) from: ({$fd}), thread:({$reactor_id})\n\n ";
    $server->send($fd, "Server: {$data} - thread({$reactor_id}): client({$fd}) \n");
});


// monitor discount 
$server->on("Close", function($server, $fd){
    echo "Client({$fd}) closed; \n";
});


// start server
$server->start();