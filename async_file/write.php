<?php

use Swoole\Coroutine;
use Swoole\Runtime;

use function Swoole\Coroutine\run;

$fileS = "./fileS.txt";
$fileL = "./fileL.txt";
$data = "1234567890";
Runtime::enableCoroutine();

echo "Coroutine start writing....\n";


run(function () use ($fileS, $fileL, $data) {
    Coroutine::create(
        function () use ($fileS, $data) {
            echo "S - : try to write content\n";
            $content = Swoole\Coroutine\System::writeFile($fileS, $data, FILE_APPEND);
            echo "S - : Content been written\n";
        }
    );

    Coroutine::create(
        function () use ($fileL) {
        echo "L - : try to write content\n";
        $content = Swoole\Coroutine\System::writeFile($fileL, FILE_APPEND);
        echo "L - : Content been written Content\n";
    });
});




echo "Coroutine write completed \n";
