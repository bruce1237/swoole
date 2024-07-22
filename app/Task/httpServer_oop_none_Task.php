<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;


class httpServerOOB
{
    protected string $host = "127.0.0.1";
    protected int $port = 9501;
    protected Server $httpServer;
    public function __construct()
    {
        $this->httpServer = new Server($this->host, $this->port);

        // $this->httpServer->set([
        //     'worker_num' => 2,         // number of worker
        //     'task_worker_num' => 4,    // number of task
        // ]);
        // register functions
        $this->httpServer->on("connect", [$this, "onConnect"]);
        $this->httpServer->on("request", [$this, "onRequest"]);
        // $this->httpServer->on("task", [$this, "onTask"]);
        // $this->httpServer->on("finish", [$this, "onFinish"]);

        $this->httpServer->start();
    }

    public function onConnect(Server $server, int $fd)
    {
        echo "Client connected: {$fd}\n";
    }

    public function onRequest(Request $request, Response $response)
    {
     $data = $request->get['abc'];

        echo "Received data: {$data}\n";

        // 将数据作为任务分配给任务进程
        // $taskId = $this->httpServer->task($data);
        // echo "Dispatched task ID: {$taskId}\n";

        // 立即响应客户端
        sleep(5);
        $response->end("hi client this is from Server\n");
    }

    // public function onTask(Server $server, int $task_id, int $worker_id, string $data)
    // {
    //     echo "Processing task ID: {$task_id}\n";

    //     // 模拟长时间运行的任务
    //     sleep(5); // 假设任务需要5秒钟完成

    //     // 返回任务结果
    //     return "Processed data: {$data}";
    // }

    // public function onFinish(Server $server, int $task_id, string $data)
    // {
    //     echo "Task ID {$task_id} finished with result: {$data}\n";
    // }
}

$server = new httpServerOOB();
