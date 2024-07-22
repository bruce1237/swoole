<?php

use Swoole\Server;

$server = new Server("127.0.0.1", 9501);

$server->set([
    'worker_num' => 2,         // 设置工作进程数量
    'task_worker_num' => 4,    // 设置任务工作进程数量
]);

// 处理客户端连接事件
$server->on("connect", function (Server $server, int $fd) {
    echo "Client connected: {$fd}\n";
});

// 处理客户端数据事件
$server->on("receive", function (Server $server, int $fd, int $reactor_id, string $data) {
    echo "Received data: {$data}\n";

    // 将数据作为任务分配给任务进程
    $taskId = $server->task($data);
    echo "Dispatched task ID: {$taskId}\n";

    // 立即响应客户端
    $server->send($fd, "Task dispatched. Task ID: {$taskId}\n");
});

// 处理任务事件
$server->on("task", function (Server $server, int $task_id, int $worker_id, string $data) {
    echo "Processing task ID: {$task_id}\n";
    
    // 模拟长时间运行的任务
    sleep(5); // 假设任务需要5秒钟完成
    
    // 返回任务结果
    return "Processed data: {$data}";
});

// 处理任务完成事件
$server->on("finish", function (Server $server, int $task_id, string $data) {
    echo "Task ID {$task_id} finished with result: {$data}\n";
});

// 启动服务器
$server->start();