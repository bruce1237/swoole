<?php

$client = new Swoole\Client(SWOOLE_SOCK_TCP);

if (!$client->connect('127.0.0.1', 9501, -1)) {
    die("Connect failed. Error: {$client->errCode}\n");
}

echo "Connected to server\n";

// 发送数据到服务器
$data = "Hello, Swoole server!";
$client->send($data);
echo "Data sent to server: {$data}\n";

// 接收服务器的响应
$response = $client->recv();
if ($response === false) {
    die("Receive failed. Error: {$client->errCode}\n");
}

echo "Response from server: {$response}\n";

// 关闭客户端
$client->close();