<?php

$client = new \Swoole\Client(SWOOLE_UDP);

$client->connect("127.0.0.1", 9502);

$client->send("HI");

$res = $client->recv();

echo $res;