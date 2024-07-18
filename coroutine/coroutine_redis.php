<?php

use Swoole\Coroutine;

use function Swoole\Coroutine\run;




echo "******************************************************\n";
echo "---------------- NOT Share COnnection ----------------\n";
echo "--under each coroutine, each connection is use to ----\n";
echo "--responsible for write/read data--------------------\n";
echo "******************************************************\n";

echo "\n\n\n";

echo "------------------ Coroutine Start  ------------------\n";

run(function() {

    // using 3 thread to read and write date
    $key = "K";
    $value = "V";

    for($i=4; $i--;){

        Coroutine::create(function () use ($key, $value, $i){
            $redis = new MyRedis();
            echo "A: set $key$i - $value\n";
            $redis->set($key.$i, $value.$i);

            
            $value = $redis->get($key.$i);
            echo "A: get $key$i value ($value)\n";
        });

        Coroutine::create(function () use ($key, $value, $i){
            $redis = new MyRedis();
            echo "B: set $key$i - $value - $i - AAAA\n";
            $redis->set($key.$i, $value.$i."AAA");

            $value = $redis->get($key.$i);
            echo "B: get $key$i value ($value)\n";
        });

        Coroutine::create(function() use ($key, $i) {
            $redis = new MyRedis();
            $redis->ttl($key.$i);
        });
    }


});


echo "============= Coroutine END =============\n";

echo "\n\n\n";

echo "******************************************************\n";
echo "------------------ Share COnnection ------------------\n";
echo "--can not sure which coroutine come back first to ----\n";
echo "--write/read data first, will mess up the result  ----\n";
echo "******************************************************\n";


$redis = new MyRedis();
run(function() use ($redis){

    // using 3 thread to read and write date
    $key = "K";
    $value = "V";

    for($i=4; $i--;){

        Coroutine::create(function () use ($redis, $key, $value, $i){
            echo "A: set $key$i - $value\n";
            $redis->set($key.$i, $value.$i);

            
            $value = $redis->get($key.$i);
            echo "A: get $key$i value ($value)\n";
        });

        Coroutine::create(function () use ($redis, $key, $value, $i){
            echo "B: set $key$i - $value - $i - AAAA\n";
            $redis->set($key.$i, $value.$i."AAA");

            $value = $redis->get($key.$i);
            echo "B: get $key$i value ($value)\n";
        });

    }
});





class MyRedis
{
    public array $data = [];
    
    public function __construct($host="localhost", $port=6379)
    {
        echo "connecting to redisServer\n";
        sleep(2);
        echo "redisServer connected\n";
    }

    public function get(string $key): string
    {
        // echo "getting ... ($key) value\n";
        sleep(3);
        $value = $this->data[$key];
        // echo "got $key value ($value)\n";
        return $value;
    }

    public function set(string $key, string $value): void
    {
        // echo "setting $key -- $value ....\n";
        sleep(3);
        // echo "$key -- $value set\n";
        $this->data[$key] = $value;
    }

    public function del(string $key): void
    {
        echo "removing $key .....\n";
        sleep(3);
        echo "$key been removed!!\n";
        if(isset($this->data[$key])) {
            unset($this->data[$key]);
        } 
    }

    public function ttl(string $key, int $time=2): void
    {
        // echo "setting TTL for $key.....\n";
        sleep(3);
        // echo "TTL for $key been set\n";
        $this->data[$key] ?? $this->data[$key]["TTL"] = $time;
    }
}