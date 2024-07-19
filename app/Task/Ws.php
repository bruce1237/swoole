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
            'worker_num' => 4, // have no idea
            "task_worker_num" => 16, // how many task can be processed at a single time
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
        for ($i = 14; $i--;) {
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
        echo "Start task - taskId: ($taskId), workerId: ($workerId) ----dataTask: ".$data['task']."---datafd:".$data['fd']."\n";
        $workload = mt_rand(1, 3);
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
        $fd = json_decode($data["task"])->fd;
        $client_task_id = json_decode($data["task"])->task;
        $msg = "task($taskId) - ($client_task_id)completed\n";

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
