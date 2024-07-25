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


echo "Log - create Table \n";
$table = new Table(8); // 8 indicate the max records can contain
// define the table
$table->column("taskName", Table::TYPE_STRING, 5);
$table->column("result", Table::TYPE_STRING, 50);

$table->create(); //create table

$processResult = [];

echo "\n\nLog - Start Coroutine SSSSSSSSSSSSSSSSSSSSSSS\n\n";
run(function () use ($taskListA,  $table, &$processResult) {


    echo "start processing TaskList - A \n";
    foreach ($taskListA as $task) {
        go(function () use ($task, $table, &$processResult) {
            echo "processTask Log - starting process task $task\n";
            $result = processTask($task);
            $processResult[] = $result; 
            $table->set(
                $task,
                [
                    "taskName" => $task,
                    "result" => $result,
                ]
            );
            echo "processTask Log - Complete process task $task\n";
        });
    }
});



echo "\n\nEnd Coroutine XXXXXXXXXXXXXXXXXXXXXXXXX\n\n";

echo "process result from table\n";
foreach ($taskListA as $task) {
    echo  $table->get($task, "result")."\n\n";
}


echo "process result from \$processResult\n";
var_dump($processResult);









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

    $processingTime = mt_rand(1, 9);

    workVerySlow($processingTime);
    return "$taskName Process Completed!";
}
