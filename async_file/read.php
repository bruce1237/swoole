<?php

use Swoole\Coroutine;
use Swoole\Runtime;
use function Swoole\Coroutine\run;


$fileS = "./fileS.txt";
$fileL = "./fileL.txt";
Runtime::enableCoroutine();

echo "Corutine start reading....\n";

run(function () use ($fileS, $fileL) {
    Coroutine::create(
        function () use ($fileS) {
            echo "S - : try to get content\n";
            $content = Swoole\Coroutine\System::readFile($fileS);
            echo "S - : got Content\n";
            echo $content . "\n";
        }
    );

    Coroutine::create(
        function () use ($fileL) {
            echo "L - : try to get content\n";
            $content = Swoole\Coroutine\System::readFile($fileL);
            echo "L - : got Content\n";
        }
    );
});


echo "Coroutine read completed\n";
