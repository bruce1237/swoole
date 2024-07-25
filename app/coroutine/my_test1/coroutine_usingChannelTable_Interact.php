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

echo "Log - create Channel \n";
$channel = new Channel(10); // 8 based on the task output

echo "Log - create Table \n";
$table = new Table(8); // 8 indicate the max records can contain
// define the table
$table->column("taskName", Table::TYPE_STRING, 5);
$table->column("result", Table::TYPE_STRING, 50);

$table->create(); //create table


echo "\n\nLog - Start Coroutine SSSSSSSSSSSSSSSSSSSSSSS\n\n";
run(function () use ($taskListA, $taskListB, $channel, $table) {


    echo "start processing TaskList - A \n";
    foreach ($taskListA as $task) {
        go(function () use ($task, $channel, $table, $taskListB) {
            $result = processTask($task);
            echo "Log - push $task into Chanel\n";
            $channel->push($result);



            $tableRecords = getTable($table, $taskListB);
            echo "Log -  get from table: ########" . json_encode($tableRecords) . " \n";
        });
    }

    echo "start processing TaskList - B \n";
    foreach ($taskListB as $task) {

        go(function () use ($task, $channel, $table, $taskListA) {
            $result = processTask($task);
            echo "Log - set $task into Table\n";
            $table->set(
                $task,
                [
                    "taskName" => $task,
                    "result" => json_encode($result),
                ]
            );


            $channelContent = getChannel($channel);
            echo "Log -  get from Channel via Function *******" . json_encode($channelContent) . "\n";
        });
    }
});
echo "\n\nEnd Coroutine XXXXXXXXXXXXXXXXXXXXXXXXX\n\n";
















function workVerySlow(int $time = 4): bool
{
    $host = 'google.com';
    exec("ping -c {$time} " . escapeshellarg($host), $output, $status);
    if ($status === 0) {
        return true;
    } else {
        echo false;
    }
}

function processTask(string $taskName): mixed
{
    echo "processTask Log - starting process task $taskName\n";
    $processingTime = mt_rand(1, 9);

    workVerySlow($processingTime);
    return [$taskName => "Process Completed!"];
}

function getTable(Table $table, array $taskList)
{
    $tableRecords = [];
    foreach ($taskList as $task) {

        if ($table->exist($task)) {
            $tableRecords[] = $table->get($task);
        }
    }

    return $tableRecords;
}

function getChannel(Channel $channel)
{
    $channelData = [];
    $channelLength = $channel->length();

    if ($channelLength > 0) {
        for ($i = $channelLength, $i > 0; $i--;) {
            $channelData[] = $channel->pop();
        }
    }
    return $channelData;
}


