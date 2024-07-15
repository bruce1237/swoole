<?php

use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Symfony\Bundle\MakerBundle\Str;

class Ws
{

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

        // set number of workers
        $this->server->set([
            'worker_num' => 20,
            "task_worker_num" => 20
        ]);
        // reg task event
        $this->server->on("task", [$this, "onTask"]);
        // finish task evetn
        $this->server->on("finish", [$this, "onTaskFinish"]);

        /**
         * from the test, client can receive the msg before the task complete
         */
        $this->server->on("message", [$this, "onReceive"]);
    }

    public function onReceive(Server $server, Frame $frame): void
    {
        echo "received MSG: ($frame->data)\n";
        echo "sending...\n";

       
        // multi-tasks
        echo "multi-tasks\n";
        for ($i = 40; $i--;) {
            $task = [
                'task' => $i,
                'fd' => $frame->fd,
            ];
            $server->task($task);
        }


        $this->server->push($frame->fd, $this->msg);
    }

    public function onTask(Server $server, int $taskId, int $workerId, mixed $data): mixed
    {
        // echo "task content: \n";
        // var_dump($data);
        echo "Start task - taskId: ($taskId), workerId: ($workerId)\n";
        $workload = mt_rand(5, 10);
        sleep($workload);
        echo "--------$workload-------\n";
        echo "task:($taskId - $workerId) finished \n";

        // send task result to onFinish 
        return [
            "taskId" => $taskId,
            "workerId" => $workerId,
            "task" => json_encode($data),
            "status" => true
        ];
    }

    public function onTaskFinish(Server $server, int $taskId, mixed $data)
    {
        echo "task($taskId) completed\n";
        // var_dump($data);
        $msg = "task($taskId) completed\n";
        $fd = json_decode($data["task"])->fd;

        $this->server->push($fd, $msg);
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
