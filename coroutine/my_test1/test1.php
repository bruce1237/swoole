<?php

use Swoole\Coroutine;

use function Swoole\Coroutine\run;

run(function () {

    for ($i = 4; $i--;) {
        $rand = mt_rand(3, 7);

        Coroutine::create(function () use ($i, $rand) {
            echo "A: routine($i): start...\n";
            echo "A: ========Workload($i): ($rand) =======\n";
            sleep($rand);
            echo "A: ------Workload ($i) Complete-----\n";
        });
    }

    for ($i=4; $i--;){
        $rand = mt_rand(3,7);

        Coroutine::create(function() use ($i, $rand){
            echo "B: routine($i): start...\n";
            echo "B: ========Workload($i): ($rand) =======\n";
            sleep($rand);
            echo "B: ------Workload ($i) Complete-----\n";
        });

    }
});
