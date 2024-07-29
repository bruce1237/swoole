<?php

declare(strict_types=1);

use Swoole\Coroutine;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;
use Swoole\Runtime;

const N = 1024;

Runtime::enableCoroutine();
$s = microtime(true);
$pool = new RedisPool((new RedisConfig)
        ->withHost('127.0.0.1')
        ->withPort(6379)
        ->withAuth('')
        ->withDbIndex(0)
        ->withTimeout(1)
);
$connection = [];

Coroutine\run(function () use ($pool, &$connection) {
    $arr = [];
    for ($n = N; $n--;) {
        Coroutine::create(function () use ($pool, $n, &$connection) {
            $redis = $pool->get();
            echo "RedisID: ------" . spl_object_id($redis) . "\n\n\n\n";
            $connection[spl_object_id($redis)] = 'AAA';
            $result = $redis->set("foo{$n}", "bar{$n}");
            if (!$result) {
                throw new RuntimeException('Set failed');
            }
            $result = $redis->get("foo{$n}");
            if ($result !== "bar{$n}") {
                throw new RuntimeException('Get failed');
            }
            $pool->put($redis);
        });
    }
});
$s = microtime(true) - $s;
echo 'Use ' . $s . 's for ' . (N * 2) . ' queries and Connection used: ' . count($connection) . PHP_EOL;;
