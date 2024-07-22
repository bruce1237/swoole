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
    'task_worker_num' => 4,
]);
$httpServer->on("workerStart", function (Server $server, int $workerId) {
    define("APP_PATH", __DIR__ . "/../webpage/");
    define("SER_PATH", __DIR__);
    require_once SER_PATH . "/pre_setup.php";
});

$httpServer->on("request", function (Request $request, Response $response) use ($httpServer) {

    $request->post['httpServer'] = $httpServer;
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

$httpServer->on("task", function(Server $server, int $task_id, int $src_worker_id, mixed $data){

    echo "TASK - processing task: ($task_id)\n";
    sleep(3);

    echo "TASK - task:($task_id) completed\n";
    return "task:($task_id) DONE\n";

});

$httpServer->on("finish", function(Server $server, int $task_id, mixed $data){
    echo "TASK - here is task:($task_id) result\n";
    echo json_encode($data);
    return true;
});

$httpServer->start();
