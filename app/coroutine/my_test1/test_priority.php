<?php
use Swoole\Coroutine;
use function Swoole\Coroutine\run;

echo "main start\n";
run(function () {
    echo "A: coro " . Coroutine::getcid() . " start\n";
    Coroutine::create(function () {
        echo "B: coro " . Coroutine::getcid() . " start\n";
        Coroutine::sleep(.2);
        echo "C: coro " . Coroutine::getcid() . " end\n";
    });
    echo "D: coro " . Coroutine::getcid() . " do not wait children coroutine\n";
    Coroutine::sleep(.1);
    echo "E: coro " . Coroutine::getcid() . " end\n";
});
echo "end\n";

/*
main start
A: coro 1 start
B: coro 2 start
D: coro 1 do not wait children coroutine
E: coro 1 end
C: coro 2 end
end

*/
