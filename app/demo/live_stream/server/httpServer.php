<?php

namespace server;

require_once "../../../vendor/autoload.php";

use server\TaskHandler;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class HttpServer
{
    protected string $host = "0.0.0.0";
    protected int $port = 80;
    protected Server $httpServer;

    public function __construct()
    {
        $this->httpServer = new Server($this->host, $this->port);
        $this->httpServer->set([
            "enable_static_handler" => true,
            "document_root" => __DIR__ . "/../webpage/",
            "worker_num" => 2,
            'task_worker_num' => 4,
        ]);

        // register functions
        $this->httpServer->on("workerStart", [$this, "onWorkStart"]);

        $this->httpServer->on("request", [$this, "onRequest"]);

        $this->httpServer->on("task", [$this, "onTask"]);

        $this->httpServer->on("finish", [$this, "onFinish"]);



        // start server
        $this->httpServer->start();
    }

    public function onWorkStart(Server $server, int $workerId)
    {
        define("APP_PATH", __DIR__ . "/../webpage/");
        define("SER_PATH", __DIR__);
        require_once SER_PATH . "/pre_setup.php";
    }

    public function onRequest(Request $request, Response $response)
    {

        $request->post['httpServer'] = $this->httpServer;
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
    }

    public function onTask(Server $server, int $task_id, int $src_worker_id, mixed $data)
    {

        // based on taskName to call different task handler
        $taskHandler = new TaskHandler();
        $taskName = $data['taskName'];
        return $taskHandler->$taskName($data['data']);
    }

    public function onFinish(Server $server, int $task_id, mixed $data)
    {
        echo "TASK onFinish Log - here is task:($task_id) result\n";
        echo "TASK onFinish Log - " . json_encode($data) . PHP_EOL;
        return true;
    }
}
