<?php

use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;


run(function () {
    // pushed more than channel can handle which is 1
    $channel = new Channel(1);
    $channel->push("A");
    $channel->push("B");
    $channel->push("C");
    $channel->push("D");

});
