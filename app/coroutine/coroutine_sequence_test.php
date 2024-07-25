<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;

run(function () {
    $channel = new Channel(1);

    // producer
    Coroutine::create(function () use ($channel) {
        for ($i = 0; $i < 10; $i++) {
           
            echo "ProducedAAAAAAAA: {$i}\n";
            workVerySlow();
        }
        // produce ended
        $channel->push(false);
    });

    // consumer
    Coroutine::create(function () use ($channel) {
        for ($i = 100; $i < 110; $i++) {
           
            echo "ProducedBBBBBBBB: {$i}\n";
            workVerySlow();
        }
        // produce ended
        $channel->push(false);
    });

    for ($i = 1000; $i < 1010; $i++) {
           
        echo "ProducedCCCCCC: {$i}\n";
        workVerySlow();
    }
    echo "--------------------\n\n\n";
});

function workVerySlow(int $time = 4): bool
{
    $host = 'google.com';
    exec("ping -c {$time} " . escapeshellarg($host), $output, $status);
    if ($status === 0) {
        return true;
    }
    return false;
}