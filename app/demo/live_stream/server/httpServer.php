<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

$host = "0.0.0.0";
$port = 80;

$httpServer = new Server($host, $port);
$httpServer->set([
    "enable_static_handler" => true,
    "document_root" => __DIR__ . "/../webpage/",
    "worker_num" => 2,
]);
$httpServer->on("workerStart", function (Server $server, int $workerId) {
    define("APP_PATH", __DIR__ . "/../webpage/");
    define("SER_PATH", __DIR__);
    require_once SER_PATH . "/pre_setup.php";
});

$httpServer->on("request", function (Request $request, Response $response) {


    $rs = callResource(
        $request->server["request_uri"],
        [
            [
                "GET" => $request->get,
                "POST" => $request->post
            ]
        ]
    );
    $response->end($rs);
});

$httpServer->start();
