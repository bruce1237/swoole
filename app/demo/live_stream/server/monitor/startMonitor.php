<?php
namespace server\monitor;

require_once "../../../../vendor/autoload.php";

$monitorServer = new MonitorServer();

swoole_timer_tick(2000, function() use ($monitorServer){
    $monitorServer->startMonitor();
});
