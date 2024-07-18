
<?php

use Swoole\Process\Pool;

$pool = new Pool(7, SWOOLE_IPC_NONE, 0, true); // 启用协程支持

$pool->on("WorkerStart", function ($pool, $workerId) {
    go(function () use ($workerId) {
        process($workerId);
    });
});

$pool->start();






function process(int $i)
{
    echo "processing ($i)....\n";
    $time = mt_rand(2, 6);
    sleep($time);
    echo "done($i)...\n";
}
