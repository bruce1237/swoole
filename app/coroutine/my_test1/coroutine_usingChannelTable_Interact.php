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







echo "\n\nLog - Start Coroutine SSSSSSSSSSSSSSSSSSSSSSS\n\n";
run(function () use ($taskListA, $taskListB, $taskListC) {
    echo "Log - create Channel \n";
    $channel = new Channel(10); // 8 based on the task output
    $channelC = new Channel(10); // 8 based on the task output
    
    echo "Log - create Table \n";
    $table = new Table(8); // 8 indicate the max records can contain
    // define the table
    $table->column("taskName", Table::TYPE_STRING, 5);
    $table->column("result", Table::TYPE_STRING, 50);
    
    $table->create(); //create table
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

    echo "start processing TaskList - B \n";
    go(function () use ($channel, $table, $taskListB) {
        foreach ($taskListB as $task) {
            $result = processTask($task);
            echo "TaskList - B - Log - set $task into Table\n";
            $table->set(
                $task,
                [
                    "taskName" => $task,
                    "result" => json_encode($result),
                ]
            );
            $channelContent = getChannel($channel);
            echo "TaskList - B - Log -  get from Channel " . json_encode($channelContent) . "\n";
        }

        echo "TaskList - B - Log - END of BBBBBBBBBBBBBBB\n\n\n\n\n";
    });


    echo "start processing TaskList - C \n";
    go(function () use ($taskListC, $table, $channelC) {
        foreach ($taskListC as $taskName) {
            echo "TaskList - C - Log - Calling multi_coroutine\n";
            $taskListCResult[] = multi_coroutine($taskName, $channelC);
            $table->set(
                "nest",
                [
                    "taskName" => "Nest",
                    "result" => json_encode($taskListCResult),
                ]
            );
        }
        echo "TaskList - C - Log - END of CCCCCCCCCCCC\n\n\n\n\n";
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

function multi_coroutine(string $taskName, Channel $channel)
{
    go(function () use ($taskName, $channel) {

        $result = [];
        for ($i = 0; $i < 4; $i++) {
            workVerySlow();
            $result[] = "$taskName +++ ";
        }
        echo "multi_coroutine Log +++ pushing to channel " . json_encode($result) . "\n";
        $channel->push($result);
    });

    go(function () use ($taskName, $channel) {
        $result = [];
        for ($i = 0; $i < 4; $i++) {
            workVerySlow();
            $result[] = "$taskName --- ";
        }
        echo "multi_coroutine Log --- pushing to channel " . json_encode($result) . "\n";
        $channel->push($result);
    });
}
