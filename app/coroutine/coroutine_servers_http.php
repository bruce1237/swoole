<?php

use Swoole\Coroutine;
use Swoole\Http\Server;

$server = new Server("127.0.0.1", 9501);

$server->on("start", function (Server $server) {
    echo "Swoole HTTP server is started at http://127.0.0.1:9501\n";
});

$server->on("request", function ($request, $response) {
    
    for($i=4; $i--;){
        go(function () use ($i) {
            process($i);
        });

    }

    $response->end("Hello, World!\n");
});

$server->start();



function process(int $i)
{
    echo "processing ($i)....\n";
    $time = mt_rand(2, 6);
    Coroutine::sleep($time);
    
    echo "done($i)...\n";
}