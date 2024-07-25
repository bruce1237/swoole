<?php

use Swoole\Http\Server as HttpServer;
use Swoole\Process;

// 定义第一个服务器类 A
class A
{
    public function __construct()
    {
        // 创建 HTTP 服务器
        $httpServer = new HttpServer("0.0.0.0", 9501);

        $httpServer->on("request", function ($request, $response) {
            $response->header("Content-Type", "text/plain");
            $response->end("Hello, this is HTTP server on port 9501");
        });

        // 启动 HTTP 服务器
        $httpServer->start();
    }
}

// 定义第二个服务器类 B
class B
{
    public function __construct()
    {
        // 创建 HTTP 服务器
        $httpServer = new HttpServer("0.0.0.0", 9502);

        $httpServer->on("request", function ($request, $response) {
            $response->header("Content-Type", "text/plain");
            $response->end("Hello, this is HTTP server on port 9502");
        });

        // 启动 HTTP 服务器
        $httpServer->start();
    }
}

// 创建子进程运行服务器 A
$processA = new Process(function () {
    new A();
}, false, 0, true);

// 创建子进程运行服务器 B
$processB = new Process(function () {
    new B();
}, false, 0, true);

// 启动子进程
$processA->start();
$processB->start();

// 等待子进程退出
Process::wait(true);
Process::wait(true);