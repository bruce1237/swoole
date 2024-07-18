<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;

run(function () {
    $channel = new Channel(1);

    // producer
    Coroutine::create(function () use ($channel) {
        for ($i = 0; $i < 10; $i++) {
            Coroutine::sleep(1.0);
            $channel->push(['rand' => rand(1000, 9999), 'index' => $i]);
            echo "Produced: {$i}\n";
        }
        // produce ended
        $channel->push(false);
    });

    // consumer
    Coroutine::create(function () use ($channel) {
        while (true) {
            // consume data
            $data = $channel->pop(2.0);
            if ($data === false) {
                assert($channel->errCode === SWOOLE_CHANNEL_TIMEOUT || $data === false);
                echo "Channel is empty or received end signal\n";
                break;
            }
            echo "got Data\n";
            var_dump($data);
        }
    });
});
