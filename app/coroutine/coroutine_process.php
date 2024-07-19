<?php


use Swoole\Process;
use Swoole\Runtime;

$process = new Process(function (Process $proc) {
    
    Runtime::enableCoroutine(true); // 启用协程支持

    for ($i=4; $i--;){
        go(function () use ($i) {
            process($i);
        });
    }
    
}, false, 2, true);

$process->start();
Process::wait(true);

function process(int $i)
{
    echo "processing ($i)....\n";
    $time = mt_rand(2, 6);
    sleep($time);
    echo "done($i)...\n";
}
