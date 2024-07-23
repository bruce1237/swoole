<?php

use function Swoole\Coroutine\run;

// use function Swoole\Coroutine\go;


echo "Start-Run\n";
run(function () {

    echo "Coroutine Start\n";

    for ($i = 4; $i--;) {
        

        go(function () use ($i) {
            echo "A: ========Workload($i): Start =======\n";
            workVerySlow();
            echo "A: ------Workload ($i) Complete-----\n";

        });
    }

    echo "sleep3";
    

    for ($i = 4; $i--;) {

        go(function () use ($i) {
            echo "B: ========Workload($i): Start =======\n";
            workVerySlow();
            echo "B: ------Workload ($i) Complete-----\n";
        });
    }
    echo "Coroutine End\n";
});

echo "End-Run\n";



function workVerySlow(int $time = 18): void
{
    
    $host = 'google.com';
    $time = mt_rand(4, $time);
    

    exec("ping -c {$time} " . escapeshellarg($host), $output, $status);

    if ($status === 0) {
        // echo "Ping Success\n";
    } else {
        // echo "Ping Failed\n";
    }
}