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

        $clientIds = $data['clientIds'];
        $server = $data['server'];
        $content = $data['content'];
        if ($server instanceof Server){
            echo "SWWWWW\n";
        } else {
            echo "NNNNNNNNNNNNN\n";

        }
        foreach ($clientIds as $fd) {
            echo "publishLive Log - $fd\n";
            if ($server->isEstablished($fd)) {
                echo "publishLive Log - push to $fd\n";
                $server->push((int)$fd, $content);
            }
        }

        return [
            "taskName" => "publishLive",
            "status" => true,
            "data" => $content,
        ];
    }
}
