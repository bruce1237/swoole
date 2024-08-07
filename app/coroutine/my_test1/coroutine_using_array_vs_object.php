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
$counter = new CC();

/**
 * using array, will send $connection default into run, everytime, that's why need to use reference
 * 
 * but for object, it is fine
 */


Coroutine\run(function () use ($pool, &$connection, $counter) {
    $arr = [];
    for ($n = N; $n--;) {
        Coroutine::create(function () use ($pool, $n, &$connection, $counter) {
            $redis = $pool->get();
            $redisID = spl_object_id($redis);
            echo "RedisID: ------" . $redisID . "\n\n\n\n";
            $connection[spl_object_id($redis)] = 'AAA';
            $counter->set($redisID, "bbbb");
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
echo 'Use ' . $s . 's for ' . (N * 2) . ' queries and Connection used: ' . count($connection) . PHP_EOL;
echo "counter size: ". $counter->getSize(). "\n\n";

class CC
{
    public array $arr=[];

    public function set($key, $value): void
    {
        $this->arr[$key] = $value;
    }

    public function get($key): string
    {
        return $this->arr[$key];
    }

    public function getSize(): int
    {
        return count($this->arr);
    }
}
