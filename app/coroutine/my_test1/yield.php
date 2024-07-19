<?php

use Swoole\Coroutine;

$cid = go(function () {
    echo "co 1 start\n";
    Coroutine::yield();
    
    echo "co 1 end\n";
});

go(function () use ($cid) {
    echo "co 2 start\n";
    Coroutine::sleep(0.5);
    Coroutine::resume($cid);
    echo "co 2 end\n";
});

Swoole\Event::wait();