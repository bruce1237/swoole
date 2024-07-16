<?php

use Swoole\Http\Request;
use Swoole\Timer;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Symfony\Bundle\MakerBundle\Str;

class Ws
{

    protected string $ip;
    protected int $port;
    protected Server $server;
    protected string $msg;
    protected int $timerId;

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

        /**
         * TIMER: Tick
         */
        // for the first client
        $msg= "\n TimerID: ";
        if ($request->fd < 4) {
            
            echo "FFFFFF\n";

            // two style:

            // swoole_timer_tick(2000, function(int $timer_id, $message){
            //     echo $message;
            // }, $msg);


            $counter = 1;
            swoole_timer_tick(2000, function(int $timer_id) use ($msg, &$counter){
                echo $msg.$timer_id;
                $this->timerId = $timer_id;
                $counter++;
                echo "Counter: ". $counter."\n";
                if ($counter == 10){
                    swoole_timer_clear($timer_id);
                    echo "Timer stopped";
                }

            });


        }

    }




    public function send(string $msg): void
    {
        $this->msg = $msg;

        /**
         * from the test, client can receive the msg before the task complete
         */
        $this->server->on("message", [$this, "onReceive"]);
    }

    public function onReceive(Server $server, Frame $frame): void
    {
        echo "received MSG: ($frame->data)\n";
        echo "sending...($this->msg)\n";

        /**
         * TIMER: After
         */
        $p1 = "A";
        $p2 = "B";

         swoole_timer_after(5000, function ($x1, $x2){
            echo $x1."-----".$x2."\n";

            // stop timer
            Timer::clear($this->timerId);

            echo "Timer: ($this->timerId) stopped \n";
         }, $p1, $p2);

       
       

        $this->server->push($frame->fd, $this->msg);
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
