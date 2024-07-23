<?php

use Swoole\Server;


class httpServerOOB
{
    protected string $host = "127.0.0.1";
    protected int $port = 9501;
    protected Server $httpServer;
    public function __construct()
    {
        $this->httpServer = new Server($this->host, $this->port);

        $this->httpServer->set([
            'worker_num' => 4,         // number of worker
            'task_worker_num' => 8,    // number of task
        ]);
        // register functions
        $this->httpServer->on("connect", [$this, "onConnect"]);
        $this->httpServer->on("receive", [$this, "onReceive"]);
        $this->httpServer->on("task", [$this, "onTask"]);
        $this->httpServer->on("finish", [$this, "onFinish"]);

        $this->httpServer->start();
    }

    public function onConnect(Server $server, int $fd)
    {
        echo "Client connected: {$fd}\n";
    }

    public function onReceive(Server $server, int $fd, int $reactor_id, string $data)
    {
        echo "Received data: ". json_encode($data)."\n";
        

        // 将数据作为任务分配给任务进程
        $taskId = $server->task($data);
        echo "Dispatched task ID: {$taskId}\n";

        // 立即响应客户端
        $server->send($fd, "Task dispatched. Task ID: {$taskId}\n");

        
    }

    public function onTask(Server $server, int $task_id, int $worker_id, string $data)
    {
        echo "Processing task ID: {$task_id}\n";

        // 模拟长时间运行的任务
        workVerySlow(); // 假设任务需要5秒钟完成

        // 返回任务结果
        return "Processed data: {$data}";
    }

    public function onFinish(Server $server, int $task_id, string $data)
    {
        echo "Task ID {$task_id} finished with result: {$data}\n";
    }
}

$server = new httpServerOOB();



function workVerySlow(int $time = 8): void
{
    
    $host = 'google.com';
    $time = mt_rand(4, $time);
    

    exec("ping -c {$time} " . escapeshellarg($host), $output, $status);

    if ($status === 0) {
        echo "Ping Success\n";
    } else {
        echo "Ping Failed\n";
    }
}