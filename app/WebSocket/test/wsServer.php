<?php

use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

$server = new \Swoole\WebSocket\Server("0.0.0.0", 9501);

$server->set(
    [
        "enable_static_handler" => true,
        "document_root" => "/home/bo/swoole/WebSocket/test",
    ]
);

$connectedCli = [];

$server->on("open", function (Server $server, Request $request) use (&$connectedCli) {
    echo "log - server: handshake success with fd({$request->fd})\n";
   $server->push($request->fd, "hi from server");
    
});


$server->on("message", function (Server $server, Frame $frame) {
    echo "log - received from Client:($frame->fd): Data: ($frame->data), opcode: ($frame->opcode}, finish: ($frame->finish) \n";
    $server->push($frame->fd, "this is from server");
});

$server->on("close", function (Server $server, string $fd) {
    echo "client: ($fd) is closed\n";
});

$server->start();

echo "server Started\n\n";

