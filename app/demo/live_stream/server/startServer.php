<?php
namespace server;

require_once "../../../vendor/autoload.php";

// using Swoole WebSocket server as it based Http server.
new WsServer();

// new HttpServer();