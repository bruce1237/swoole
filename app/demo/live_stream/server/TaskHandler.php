<?php
namespace server;

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
            "data"=>[]
        ];
    }
}