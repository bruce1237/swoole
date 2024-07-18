<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;


$host = "127.0.0.1";
$port =9501;

$server = new Server($host, $port, SWOOLE_BASE);

$server->on("request", function(Request $request, Response $response){
    
    $output = "";

    $channel = new Channel(2);

    go(function() use ($channel){
        $rs = process("hello");
        $channel->push($rs);
    });

    go(function() use ($channel){
        $rs = process("world");
        $channel->push($rs);
    });

    while (true) {
        // consume data
        $data = $channel->pop(4.0);
        if ($data === false) {
            assert($channel->errCode === SWOOLE_CHANNEL_TIMEOUT || $data === false);
            echo "Channel is empty or received end signal\n";
            break;
        }
        echo "got Data\n";
        $output .= $data;
    }

    echo ">>>>>$output<<<<<\n";

    $response->end("<h1>$output</h1>");
});

$server->start();


function process(string $data): string
{
    echo "processing ($data)....\n";
    $time = mt_rand(1, 3);
    Coroutine::sleep($time);
    echo "done($data)...\n";
    return $data;
}

