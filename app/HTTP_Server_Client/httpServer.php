<?php

// create new http server with address of localhost and port 9501
$httpServer = new \Swoole\Http\Server("127.0.0.1", 9501);

// config http server to handle static page
// when request static page, will skip $response in the 
// $httpServer->on() code block
$httpServer->set(
    [
        'enable_static_handler' => true,  
        'document_root' => "/home/bo/swoole_testCode/HTTP_Server_Client/static_page/",
    ]
);


$httpServer->on("request", function ($request, $response) {
    var_dump($request);
    $response->cookie("NaMe", "VaLue", time()+1800);
    $response->end("<h1>Http server is working</h1>");
});


$httpServer->start();
