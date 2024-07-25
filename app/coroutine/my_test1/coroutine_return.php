<?php

use Swoole\Coroutine\Channel;
use Swoole\Table;

use function Swoole\Coroutine\run;




$taskListA = [
    "taskA",
    "taskB",
    "taskC",
    "taskD",
];

$taskListB = [
    "taskW",
    "taskX",
    "taskY",
    "taskZ",
];

$taskListC = [
    "taskListA",
    "taskListB",
];


echo "Log - create Channel \n";

$channel = new Channel(10); // 8 based on the task output
$channelC = new Channel(10); // 8 based on the task output

echo "Log - create Table \n";
$table = new Table(8); // 8 indicate the max records can contain
// define the table
$table->column("taskName", Table::TYPE_STRING, 5);
$table->column("result", Table::TYPE_STRING, 50);

$table->create(); //create table


echo "\n\nLog - Start Coroutine SSSSSSSSSSSSSSSSSSSSSSS\n\n";
run(function () use ($taskListA, $taskListB, $taskListC, $channel, $channelC, $table) {
    

    echo "start processing TaskList - A \n";
    go(function () use ($channel, $table, $taskListB, $taskListA) {
        foreach ($taskListA as $task) {
            $result = processTask($task);
            echo "TaskList - A - Log - push $task into Chanel\n";
            $channel->push($result);


            $tableRecords = getTable($table, $taskListB);
            echo "TaskList - A - Log -  get from table: ########" . json_encode($tableRecords) . " \n";
        }
        echo "TaskList - A - Log -  END of AAAAAAAAAAAAA\n\n\n\n\n";
    });




});



echo "\n\nEnd Coroutine XXXXXXXXXXXXXXXXXXXXXXXXX\n\n";












function workVerySlow(int $time = 4): bool
{
    $host = 'google.com';
    exec("ping -c {$time} " . escapeshellarg($host), $output, $status);
    if ($status === 0) {
        return true;
    }
    return false;
}

function processTask(string $taskName): mixed
{
    echo "processTask Log - starting process task $taskName\n";
    $processingTime = mt_rand(1, 9);

    workVerySlow($processingTime);
    return [$taskName => "Process Completed!"];
}

