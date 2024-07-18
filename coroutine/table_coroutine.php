<?php

use Swoole\Table;
use Swoole\Http\Server;
use Swoole\Coroutine;

// 创建 Swoole Table
$table = new Table(1024);
$table->column('data', Table::TYPE_STRING, 64);
$table->create();

// 创建 HTTP 服务器
$server = new Server("127.0.0.1", 9501);

$server->on("start", function (Server $server) {
    echo "Swoole HTTP server is started at http://127.0.0.1:9501\n";
});

$server->on("request", function ($request, $response) use ($table) {
    go(function () use ($table) {
        // 协程1: 生产数据
        $data = "Hello from coroutine 1";
        $table->set('key1', ['data' => $data]);
        echo "Coroutine 1 set data: $data\n";
    });

    go(function () use ($table) {
        // 协程2: 消费数据
        Coroutine::sleep(1); // 模拟处理延迟
        $data = $table->get('key1', 'data');
        echo "Coroutine 2 got data: $data\n";
    });

    $response->end("Request handled\n");
});

$server->start();