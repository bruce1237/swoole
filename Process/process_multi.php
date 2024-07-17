<?php
// multi process

use PhpParser\Parser\Multiple;
use Swoole\Coroutine;
use Swoole\Process;
use Swoole\Runtime;

$files = [
    "php-8.3",
    "php-8.2",
    "php-8.1",
];

$complexFiles = [
    "php8" => [
        "php-8.3",
        "php-8.2",
        "php-8.1",
    ],
    "php7" => [
        "php-7.3",
        "php-7.2",
        "php-7.1",
    ],
    "php5" => [
        "php-5.3",
        "php-5.2",
        "php-5.1",
    ],
];

// singleProcess($files);

multiProcess($files);
// multiProcessCoroutine($complexFiles);







function singleProcess($files): void
{
    echo "-------------- Single-process -----------\n";

    $start = microtime(true);

    foreach ($files as $file) {
        download($file, "Single");
    }

    echo "Single - DONE: " . microtime(true) - $start . "\n";
}

function multiProcess($files): void
{
    echo "============== Multi-process ========\n";


    $processIds = [];
    $start = microtime(true);


    foreach ($files as $file) {
        $process = new Process(
            function () use ($file) {
                download($file, "Multi");
            }
        );
        $pid = $process->start();
        echo "PID: $pid\n";
        $processIds[$pid] = $process;
    }

    // recycle process
    foreach ($processIds as $pid => $process) {
        $status = Process::wait(true);
        echo "log - Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
    }
    echo "Multi DONE: " . microtime(true) - $start . "\n";
}


function multiProcessCoroutine($complexFiles): void
{
    echo "****************** Coroutine-process ******************\n";

    $processIds = [];
    $start = microtime(true);

    foreach ($complexFiles as $major => $files) {
        $process = new Process(
            function () use ($major, $files) {

                echo "Start Process $major \n";
                // enable coroutine
                Runtime::enableCoroutine(true);
                foreach ($files as $file) {

                    // start Coroutine
                    Coroutine::create(function () use ($file) {
                        download($file, "Coroutine");

                        // use signal to stop process
                        Process::signal(SIGTERM, function () {
                            echo "Received SIGTERM, terminating process...\n";

                            exit(0);
                        });
                    });
                }
            },
            // enable coroutine 
            // enable_coroutine:true,
        );

        $pid = $process->start();
        echo "PID: $pid\n";
        $processIds[$pid] = $process;
    }

    foreach ($processIds as $pid => $process) {
        $status = Process::wait(true);
        if ($status) {
            echo "log - Recycled #{$status['pid']}, code={$status['code']}, signal={$status['signal']}" . PHP_EOL;
        }
    }

    echo "End Process $major \n";

    echo "Coroutine DONE: " . microtime(true) - $start . "\n";
}


function download(string $file, string $mode): void
{
    echo "$mode download $file Started\n";
    sleep(5);
    echo "$mode  download $file Completed\n";
}
