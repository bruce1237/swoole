<?php

use Swoole\Coroutine\Scheduler;
use Swoole\Coroutine;

$scheduler = new Scheduler();

$scheduler->add(function () {
    echo "Task 1 Started \n";
    Coroutine::sleep(2);
    echo "Task 1 completed\n";
});

$scheduler->add(function () {
    echo "Task 2 Started \n";
    Coroutine::sleep(1);
    echo "Task 2 completed\n";
});

$scheduler->start();

echo "All tasks completed\n";