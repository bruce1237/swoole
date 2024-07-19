<?php

// create client object
$client = new \Swoole\Client(SWOOLE_TCP);

// connect tcpServer
if (!$client->connect("127.0.0.1", 9501)){
    echo "log - connect failed!!\n";
    exit;
}
echo "log - connect Success";

// send msg to server
$client->send("hi, hello");

// get input from user
fwrite(STDOUT, "msg: ");
$msg = trim(fgets(STDIN));

// send user msg to server
$msgLength = $client->send($msg);

if($msgLength == strlen($msg)) {
    echo "log - send success \n";
} else {
    echo "log - send Failed \n";
}


// receive from server
$rec = $client->recv();

if (empty($rec)) {
    echo "log - server not available! \n";
}

if ($rec === false) {
    echo "log - receive from Server failed \n";
}

if ($rec) {
    echo "received from Server: {$rec}\n";
}