<?php

namespace server;

require_once "../../../vendor/autoload.php";

use Redis;
use server\TaskHandler;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WsServer
{
    protected string $host = "0.0.0.0";
    protected int $port = 9501;
    protected int $chatRoomPort = 9502;
    protected Server $wsServer;

    public function __construct()
    {
        $this->wsServer = new Server($this->host, $this->port, SWOOLE_PROCESS);
        $this->wsServer->set([
            "enable_static_handler" => true,
            "document_root" => __DIR__ . "/../webpage/",
            "worker_num" => 4,
            'task_worker_num' => 8,
        ]);
        $this->wsServer->listen($this->host, $this->chatRoomPort, SWOOLE_TCP);

        // register http server functions
        $this->wsServer->on("workerStart", [$this, "onWorkStart"]);

        $this->wsServer->on("request", [$this, "onRequest"]);

        $this->wsServer->on("task", [$this, "onTask"]);

        $this->wsServer->on("finish", [$this, "onFinish"]);

        // register websocket server function
        $this->wsServer->on("open", [$this, "onOpen"]);
        $this->wsServer->on("message", [$this, "onMessage"]);
        $this->wsServer->on("close", [$this, "onClose"]);
        
        // start server
        $this->wsServer->start();
    }

    public function onWorkStart(Server $server, int $workerId)
    {
        define("APP_PATH", __DIR__ . "/../webpage/");
        define("SER_PATH", __DIR__);
        require_once SER_PATH . "/pre_setup.php";
        
        // clear connected client ids
        getRedis()->del("connectedClientIds");
        
    }

    public function onRequest(Request $request, Response $response)
    {
        $rs = callResource(
            $request->server["request_uri"],
            [
                [
                    "GET" => $request->get,
                    "POST" => $request->post,
                    "Server" => $this->wsServer,
                ]
            ]
        );
        $response->end(json_encode($rs));
    }

    public function onTask(Server $server, int $task_id, int $src_worker_id, mixed $data)
    {
        // based on taskName to call different task handler
        $taskHandler = new TaskHandler();
        $taskName = $data['taskName'];
        $data['data']['server'] = $server;
        return $taskHandler->$taskName($data['data']);
    }

    public function onFinish(Server $server, int $task_id, mixed $data)
    {
        echo "TASK onFinish Log - here is task:($task_id) result\n";
        echo "TASK onFinish Log - " . json_encode($data) . PHP_EOL;
        return true;
    }

    public function onOpen(Server $server, Request $request)
    {
        $redis = getRedis();
        $redis->sAdd("connectedClientIds", $request->fd);
        echo "onOpen Log - ($request->fd) connected \n";
    }

    public function onClose(Server $server, int $fd)
    {
        $redis = getRedis();
        $redis->sRem("connectedClientIds", $fd);
        echo "onClose Log - ($fd) disconnected \n";
    }

    public function onMessage(Server $server, Frame $frame)
    {
        // $server->push($frame->fd, "hi from server");
    }
}
