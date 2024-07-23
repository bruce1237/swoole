<?php

function callResource(string $uri, array $params = []): mixed
{
    $info = analysisUri($uri);
    if ($info){
        require_once $info['file'];
        $obj = new $info['class'];
        return call_user_func_array([$obj, $info['function']], $params);
    }
    return false;

}

function analysisUri(string $uri): array|false
{

    $uri = explode("/", $uri);


    if (!$uri || sizeof($uri) != 3) {
        return false;
    }

    return [
        "class" => "webpage\\" . $uri[1],
        "function" => $uri[2],
        "file" => APP_PATH . ucfirst($uri[1]) . ".php",
    ];
}

function workVerySlow(int $time = 4): void
{
    
    $host = 'google.com';
    

    exec("ping -c {$time} " . escapeshellarg($host), $output, $status);

    if ($status === 0) {
        echo "Ping Success\n";
    } else {
        echo "Ping Failed\n";
    }
}

function getRedis(): Redis
{
    $host = "127.0.0.1";
    $port = 6379;
    $redis = new Redis();
    $redis->connect($host, $port);
    $redis->select(0);
    return $redis;
}
