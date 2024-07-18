<?php

use Swoole\Coroutine;

use function Swoole\Coroutine\run;

use function Swoole\Coroutine\go;


echo "Start-Run\n";
run(function () {

    echo "Coroutine Start\n";

    for ($i = 4; $i--;) {
        $rand = mt_rand(3, 7);

        go(function () use ($i, $rand) {
            echo "A: routine($i): start...\n";
            echo "A: ========Workload($i): ($rand) =======\n";
            sleep($rand);
            echo "A: ------Workload ($i) Complete-----\n";
        });
    }

    echo "sleep3";
    sleep(3);

    for ($i = 4; $i--;) {
        $rand = mt_rand(3, 7);

        go(function () use ($i, $rand) {
            echo "B: routine($i): start...\n";
            echo "B: ========Workload($i): ($rand) =======\n";
            sleep($rand);
            echo "B: ------Workload ($i) Complete-----\n";
        });
    }
    echo "Coroutine End\n";
});

echo "End-Run\n";