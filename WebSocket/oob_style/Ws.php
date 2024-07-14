<?php

use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class Ws {

    protected string $ip;
    protected int $port;
    protected Server $server;
    protected string $msg;

    public function __construct(string $ip = "0.0.0.0", int $port = 9501)
    {
        $this->server = new Server($ip, $port);
    }

    public function connect(): void
    {
        $this->server->on("open", [$this, 'onOpens']);
    }

    public function onOpens(Server $server, Request $request): void
    {
        echo "connected client: ($request->fd)\n";
    }




    public function send(string $msg): void
    {
        $this->msg = $msg;
        $this->server->on("message", [$this, "onReceive"]);
        // $this->server->on("message", [$this, "onSend"]);
    }

    public function onReceive(Server $server, Frame $frame): void
    {
        echo "received MSG: ($frame->data)\n";
        echo "sending...\n";
        $this->server->push($frame->fd, $this->msg);
    }

    public function onSend(Frame $frame, string $msg): void
    {
        echo "sending...\n";
        $this->server->push($frame->fd, $msg);
    }


    public function close(): void
    {
        $this->server->on("close", [$this, "onClose"]);
    }

    public function onClose(Server $server, int $fd): void
    {
        echo "close: ({$fd}) \n";
    }


    public function start(): void
    {
        $this->server->start();
    }

}