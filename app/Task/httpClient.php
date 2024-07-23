<?php

$client = new Swoole\Client(SWOOLE_SOCK_TCP);

if (!$client->connect('127.0.0.1', 9501, -1)) {
    die("Connect failed. Error: {$client->errCode}\n");
}

echo "Connected to server\n";


for ($i = 5; $i--;) {
    $client->send("task{$i}");
    echo "Data sent to server: task{$i}\n";
    
    // response from server
    $response = $client->recv();
    if ($response === false) {
        die("Receive failed. Error: {$client->errCode}\n");
    }
    echo "Response from server: {$response}\n";
}






// close 
$client->close();
