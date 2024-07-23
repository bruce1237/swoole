<?php

namespace server;

use Swoole\WebSocket\Server;

class TaskHandler
{
    public function sendSmsCode(array $data): array
    {
        echo "taskHandler Log - sendSmsCode\n";
        echo "taskHandler Log - Mobile: {$data['mobile']}, Code: {$data['code']}\n";
        echo "taskHandler Log - this is going to task 3 seconds\n";

        workVerySlow();

        echo "taskHandler Log - SmsCode sent successful\n";
        return [
            "taskName" => "sendSmsCode",
            "status" => true,
            "data" => []
        ];
    }

    public function publishLive($data): array
    {
                // get connected client
                $redis = getRedis();
                $clientIds = $redis->sMembers("connectedClientIds");

        
        $server = $data['server'];
        $content = $data['content'];
        if ($server instanceof Server) {
            echo "SWWWWW\n";
        } else {
            echo "NNNNNNNNNNNNN\n";
        }

        for ($i = 100; $i--;) {
            echo "publishLive Log - $i ---\n";

            foreach ($clientIds as $fd) {
                echo "publishLive Log - FD: $fd\n";
                // if ($server->isEstablished($fd)) {
                    echo "publishLive Log - ($i)push to $fd\n";
                    $server->push((int)$fd, $content);
                // }
            }
            sleep(1);
        }

        return [
            "taskName" => "publishLive",
            "status" => true,
            "data" => $content,
        ];
    }
}
