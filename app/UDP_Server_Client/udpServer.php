<?php

// create udp server
$server = new \Swoole\Server("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_UDP);

// monitor incomming package
$server->on("packet", function ($server, $data, $clientInfo) {
    var_dump($clientInfo);
    $server->sendto($clientInfo['address'], $clientInfo['port'], "Server: {$data}\n");
});

$server->start();
