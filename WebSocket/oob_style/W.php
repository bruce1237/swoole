W
<?php

use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class Ws {

    protected string $ip;
    protected int $port;
    protected Server $server;

    public function __construct(string $ip = "0.0.0.0", int $port = 9501)
    {
        $this->server = new Server($ip, $port);
    }

    public function connect(): void
    {
        $this->server->on("open", [$this, 'onOpen']);
    }

    public function onOpen(Server $server, Request $request): void
    {
        var_dump($request->fd);
    }




    public function send(): void
    {
        $this->server->on("message", [$this, "onMessage"]);
    }

    public function onMessage(Server $server, Frame $frame): void
    {
        echo "Received MSG: ($frame->data)\n";
        // 在接收到消息时发送回应
        $server->push($frame->fd, "Server: " . $frame->data);
    }

    public function close(): void
    {
        $this->server->on("close", [$this, "onClose"]);
    }

    public function onClose(Server $server, int $fd): void
    {
        echo "Client $fd closed\n";
    }

    public function start(): void
    {
        $this->server->start();
    }
}